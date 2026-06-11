<?php
/**
 * FabX ERP - Authentication Controller
 * Login, Logout, Password Reset, Session Management
 */

namespace Modules\Auth;

use Core\Controller;
use Core\Database;

class AuthController extends Controller {
    protected string $module = 'auth';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Show login page
     */
    public function login(): void {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        
        $data = [
            'page_title' => 'Login - ' . APP_NAME,
            'body_class' => 'login-page',
            'error' => $_GET['error'] ?? '',
            'timeout' => isset($_GET['timeout'])
        ];
        
        extract($data);
        require_once FABX_ROOT . '/modules/auth/views/login.php';
    }

    /**
     * Process login
     */
    public function authenticate(): void {
        if (!is_post()) {
            $this->redirect('/auth/login');
        }

        // CSRF check
        if (!validate_csrf()) {
            $this->flash('error', 'Invalid security token. Please try again.');
            $this->redirect('/auth/login');
        }

        $email = input('email');
        $password = (string)input_raw('password', '');
        $remember = isset($_POST['remember']);

        if (empty($email) || $password === '') {
            $this->flash('error', 'Please enter both email and password.');
            $this->redirect('/auth/login');
        }

        // Rate limiting
        if (!check_rate_limit('login_' . $email, 5, 15)) {
            $this->flash('error', 'Too many login attempts. Please try again after 15 minutes.');
            log_security_event('RATE_LIMIT_EXCEEDED', "Login attempts for: $email");
            $this->redirect('/auth/login');
        }

        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name, r.permissions, d.name as department_name
             FROM " . $this->db->table("users") . " u
             LEFT JOIN " . $this->db->table("roles") . " r ON u.role_id = r.id
             LEFT JOIN " . $this->db->table("departments") . " d ON u.department_id = d.id
             WHERE u.email = ? AND u.status = 'active' AND u.is_deleted = 0",
            [$email]
        );

        // Check the lock before verifying the password so a locked account
        // cannot be brute-forced at all.
        if ($user && (int)$user['failed_attempts'] >= MAX_FAILED_LOGINS
            && $user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $this->flash('error', 'Account is temporarily locked due to repeated failed logins. Try again later or contact admin.');
            log_security_event('LOCKED_LOGIN_ATTEMPT', "Email: $email");
            $this->redirect('/auth/login');
        }

        if (!$user || !verify_password($password, $user['password'])) {
            if ($user) {
                $this->recordFailedAttempt((int)$user['id'], (int)$user['failed_attempts']);
            }
            $this->flash('error', 'Invalid email or password.');
            log_security_event('FAILED_LOGIN', "Email: $email, IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            $this->redirect('/auth/login');
        }

        // Successful login
        $this->resetFailedAttempts($user['id']);
        session_regenerate_id(true);

        // Set session
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'];
        $_SESSION['user_role_id'] = (int)$user['role_id'];
        $_SESSION['user_department'] = $user['department_name'];
        $_SESSION['user_department_id'] = (int)$user['department_id'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? '';
        $_SESSION['user_permissions'] = json_decode($user['permissions'] ?? '[]', true);
        $_SESSION['last_activity'] = time();
        $_SESSION['login_time'] = time();

        // Remember me: cookie holds the raw token, DB stores its hash
        if ($remember) {
            $token = generate_token(64);
            $this->db->execute(
                "UPDATE " . $this->db->table("users") . " SET remember_token = ? WHERE id = ?",
                [hash('sha256', $token), $user['id']]
            );
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            setcookie('remember_me', $token, time() + 86400 * 30, '/', '', $secure, true);
        }

        // Log activity
        log_activity('LOGIN', 'User logged in successfully', $user['id']);
        log_security_event('SUCCESSFUL_LOGIN', "User: {$user['email']}");

        $this->flash('success', 'Welcome back, ' . $_SESSION['user_name'] . '!');
        $this->redirect('/dashboard');
    }

    /**
     * Logout
     */
    public function logout(): void {
        $userId = current_user_id();
        
        // Clear remember token
        if ($userId) {
            $this->db->execute(
                "UPDATE " . $this->db->table("users") . " SET remember_token = NULL WHERE id = ?",
                [$userId]
            );
        }
        
        // Clear cookie
        setcookie('remember_me', '', time() - 3600, '/');
        
        // Log activity
        log_activity('LOGOUT', 'User logged out', $userId);

        // Destroy session, then start a clean one so the flash message survives
        session_destroy();
        session_start();
        session_regenerate_id(true);

        $this->flash('info', 'You have been logged out successfully.');
        $this->redirect('/auth/login');
    }

    /**
     * Show forgot password page
     */
    public function forgotPassword(): void {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        
        $data = ['page_title' => 'Forgot Password - ' . APP_NAME];
        extract($data);
        require_once FABX_ROOT . '/modules/auth/views/forgot_password.php';
    }

    /**
     * Process forgot password
     */
    public function sendResetLink(): void {
        if (!is_post()) {
            $this->redirect('/auth/forgot-password');
        }

        $email = input('email');

        if (empty($email) || !is_valid_email($email)) {
            $this->flash('error', 'Please enter a valid email address.');
            $this->redirect('/auth/forgot-password');
        }

        $user = $this->db->fetchOne(
            "SELECT id, first_name, last_name FROM " . $this->db->table("users") . " WHERE email = ? AND status = 'active'",
            [$email]
        );

        if ($user) {
            $token = generate_token(32);
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->db->execute(
                "UPDATE " . $this->db->table("users") . "
                 SET reset_token = ?, reset_expires = ? WHERE id = ?",
                [$token, $expires, $user['id']]
            );

            $resetLink = base_url('auth/reset-password?token=' . urlencode($token));
            send_email(
                $email,
                APP_NAME . ' - Password Reset',
                "<p>Hello " . e($user['first_name']) . ",</p>" .
                "<p>A password reset was requested for your account. The link below is valid for 1 hour:</p>" .
                "<p><a href=\"{$resetLink}\">{$resetLink}</a></p>" .
                "<p>If you did not request this, you can ignore this email.</p>"
            );

            log_activity('PASSWORD_RESET_REQUEST', "Reset requested for: $email", $user['id']);
        }

        // Always show success to prevent email enumeration
        $this->flash('success', 'If an account exists with this email, you will receive a password reset link.');
        $this->redirect('/auth/forgot-password');
    }

    /**
     * Show reset password page
     */
    public function resetPassword(): void {
        if (is_logged_in()) {
            redirect('/dashboard');
        }
        
        $token = input('token');
        
        if (empty($token)) {
            $this->flash('error', 'Invalid reset link.');
            $this->redirect('/auth/login');
        }

        $user = $this->db->fetchOne(
            "SELECT id FROM " . $this->db->table("users") . " 
             WHERE reset_token = ? AND reset_expires > NOW()",
            [$token]
        );

        if (!$user) {
            $this->flash('error', 'Reset link has expired or is invalid.');
            $this->redirect('/auth/forgot-password');
        }

        $data = [
            'page_title' => 'Reset Password - ' . APP_NAME,
            'token' => $token
        ];
        extract($data);
        require_once FABX_ROOT . '/modules/auth/views/reset_password.php';
    }

    /**
     * Process password reset
     */
    public function updatePassword(): void {
        if (!is_post()) {
            $this->redirect('/auth/login');
        }

        $token = input('token');
        $password = (string)input_raw('password', '');
        $confirmPassword = (string)input_raw('confirm_password', '');

        if (empty($token) || $password === '' || $confirmPassword === '') {
            $this->flash('error', 'All fields are required.');
            $this->redirect('/auth/reset-password?token=' . urlencode($token));
        }

        if ($password !== $confirmPassword) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect('/auth/reset-password?token=' . urlencode($token));
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters long.');
            $this->redirect('/auth/reset-password?token=' . urlencode($token));
        }

        $user = $this->db->fetchOne(
            "SELECT id FROM " . $this->db->table("users") . " 
             WHERE reset_token = ? AND reset_expires > NOW()",
            [$token]
        );

        if (!$user) {
            $this->flash('error', 'Reset link has expired.');
            $this->redirect('/auth/forgot-password');
        }

        $hashedPassword = hash_password($password);
        
        $this->db->execute(
            "UPDATE " . $this->db->table("users") . " 
             SET password = ?, reset_token = NULL, reset_expires = NULL, 
                 password_changed_at = NOW(), failed_attempts = 0, locked_until = NULL 
             WHERE id = ?",
            [$hashedPassword, $user['id']]
        );

        log_activity('PASSWORD_RESET', 'Password reset successful', $user['id']);
        $this->flash('success', 'Password has been reset successfully. Please login with your new password.');
        $this->redirect('/auth/login');
    }

    /**
     * Show profile page
     */
    public function profile(): void {
        require_auth();
        
        $user = $this->db->fetchOne(
            "SELECT u.*, r.name as role_name, d.name as department_name 
             FROM " . $this->db->table("users") . " u
             LEFT JOIN " . $this->db->table("roles") . " r ON u.role_id = r.id
             LEFT JOIN " . $this->db->table("departments") . " d ON u.department_id = d.id
             WHERE u.id = ?",
            [current_user_id()]
        );
        
        $this->view('profile', [
            'page_title' => 'My Profile',
            'user' => $user
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(): void {
        require_auth();
        
        if (!is_post() || !validate_csrf()) {
            $this->json(false, 'Invalid request.');
        }

        $firstName = input('first_name');
        $lastName = input('last_name');
        $phone = input('phone');
        $currentPassword = (string)input_raw('current_password', '');
        $newPassword = (string)input_raw('new_password', '');

        if (empty($firstName) || empty($lastName)) {
            $this->json(false, 'First name and last name are required.');
        }

        $userId = current_user_id();
        $updates = [];
        $params = [];

        if ($firstName) {
            $updates[] = "first_name = ?";
            $params[] = $firstName;
        }
        if ($lastName) {
            $updates[] = "last_name = ?";
            $params[] = $lastName;
        }
        if ($phone) {
            $updates[] = "phone = ?";
            $params[] = $phone;
        }

        // Handle avatar upload
        if (!empty($_FILES['avatar']['name'])) {
            $upload = upload_file($_FILES['avatar'], 'profile_pics');
            if ($upload['success']) {
                $updates[] = "avatar = ?";
                $params[] = $upload['path'];
            }
        }

        // Password change
        if ($newPassword !== '') {
            if ($currentPassword === '') {
                $this->json(false, 'Please enter your current password to set a new one.');
            }

            $user = $this->db->fetchOne(
                "SELECT password FROM " . $this->db->table("users") . " WHERE id = ?",
                [$userId]
            );

            if (!$user || !verify_password($currentPassword, $user['password'])) {
                $this->json(false, 'Current password is incorrect.');
            }

            if (strlen($newPassword) < 8) {
                $this->json(false, 'New password must be at least 8 characters.');
            }

            $updates[] = "password = ?";
            $params[] = hash_password($newPassword);
            $updates[] = "password_changed_at = NOW()";
        }

        if (!empty($updates)) {
            $params[] = $userId;
            $this->db->execute(
                "UPDATE " . $this->db->table("users") . " SET " . implode(', ', $updates) . " WHERE id = ?",
                $params
            );
        }

        // Update session name
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;

        log_activity('PROFILE_UPDATE', 'Profile updated', $userId);
        $this->json(true, 'Profile updated successfully.');
    }

    /**
     * Toggle theme
     */
    public function toggleTheme(): void {
        $current = $_SESSION['theme'] ?? 'light';
        $_SESSION['theme'] = $current === 'light' ? 'dark' : 'light';
        $this->json(true, 'Theme updated.', ['theme' => $_SESSION['theme']]);
    }

    /**
     * Toggle sidebar
     */
    public function toggleSidebar(): void {
        $_SESSION['sidebar_collapsed'] = !($_SESSION['sidebar_collapsed'] ?? false);
        $this->json(true, 'Sidebar toggled.');
    }

    /**
     * Keep session alive (heartbeat)
     */
    public function heartbeat(): void {
        if (is_logged_in()) {
            $idle = time() - ($_SESSION['last_activity'] ?? time());
            $_SESSION['last_activity'] = time();
            $this->json(true, 'Session active', [
                'session_remaining' => max(0, SESSION_TIMEOUT - $idle)
            ]);
        }
        $this->json(false, 'Not authenticated');
    }

    /**
     * Reset failed login attempts
     */
    private function resetFailedAttempts(int $userId): void {
        $this->db->execute(
            "UPDATE " . $this->db->table("users") . "
             SET failed_attempts = 0, locked_until = NULL, last_login = NOW()
             WHERE id = ?",
            [$userId]
        );
    }

    /**
     * Record a failed login attempt; lock the account once the limit is hit
     */
    private function recordFailedAttempt(int $userId, int $currentAttempts): void {
        $attempts = $currentAttempts + 1;
        if ($attempts >= MAX_FAILED_LOGINS) {
            $this->db->execute(
                "UPDATE " . $this->db->table("users") . "
                 SET failed_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL " . (int)LOCKOUT_MINUTES . " MINUTE)
                 WHERE id = ?",
                [$attempts, $userId]
            );
            log_security_event('ACCOUNT_LOCKED', "User #{$userId} locked after {$attempts} failed attempts");
        } else {
            $this->db->execute(
                "UPDATE " . $this->db->table("users") . " SET failed_attempts = ? WHERE id = ?",
                [$attempts, $userId]
            );
        }
    }
}
