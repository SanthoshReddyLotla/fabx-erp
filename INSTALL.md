# FabX Engineering ERP - Installation Guide

## Complete Industrial ERP + CRM + QMS System

---

## System Requirements

### Server Requirements
- **PHP**: 8.0 or higher (8.2 recommended)
- **MySQL**: 8.0 or higher / MariaDB 10.5+
- **Web Server**: Apache 2.4+ with mod_rewrite
- **Memory**: 512MB minimum, 1GB recommended
- **Storage**: 5GB minimum (for application + file uploads)

### Required PHP Extensions
```
- mysqli / pdo_mysql
- mbstring
- json
- fileinfo
- openssl
- gd / imagick
- zip
- xml
- curl
- session
- intl
```

### Browser Support
- Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- JavaScript enabled
- Cookies enabled

---

## Installation Steps

### Step 1: Download and Extract

```bash
# Upload the  folder to your web server document root
# Example for Apache:
/var/www/html/

# Or for shared hosting:
/public_html/
```

### Step 2: Set File Permissions

```bash
cd /var/www/html/

# Set ownership (adjust www-data to your web server user)
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Make upload and log directories writable
sudo chmod -R 775 assets/uploads/
sudo chmod -R 775 logs/
sudo chmod -R 775 backup/
```

### Step 3: Create MySQL Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE fabx_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create database user (recommended for security)
CREATE USER 'fabx_user'@'localhost' IDENTIFIED BY 'YourStrongPassword123!';
GRANT ALL PRIVILEGES ON fabx_erp.* TO 'fabx_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Import Database Schema

```bash
# Import the schema
cd /var/www/html/database
mysql -u fabx_user -p fabx_erp < schema.sql

# Import seed data (optional - for demo/testing)
mysql -u fabx_user -p fabx_erp < seed.sql
```

### Step 5: Configure Application

Edit `config/config.php` and update the database credentials:

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fabx_erp');
define('DB_USER', 'fabx_user');           // Your database username
define('DB_PASS', 'YourStrongPassword123!'); // Your database password
```

Update the application URL:

```php
define('APP_URL', 'http://your-domain.com/');
// OR for local development:
define('APP_URL', 'http://localhost/');
```

For production, also update:

```php
define('APP_ENV', 'production');
define('ENCRYPTION_KEY', 'your-random-32-char-key-here!!'); // Change this!
```

### Step 6: Configure Apache

Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

For Apache virtual host, ensure AllowOverride is set to All:

```apache
<Directory /var/www/html>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

The `.htaccess` file is already included in the project root.

### Step 7: Default Login Credentials

After installation (with seed data):

```
URL: http://your-domain.com/auth/login
Email: admin@fabxengineering.com
Password: Admin@123

**IMPORTANT**: Change the default password immediately after first login!
```

If you didn't use seed data, create a Super Admin manually:

```sql
INSERT INTO fabx_users (employee_code, first_name, last_name, email, phone, password, role_id, department_id, designation, status)
VALUES ('FABX001', 'System', 'Administrator', 'admin@fabxengineering.com', '9999999999', 
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1, 'System Admin', 'active');
-- Password hash above is for "password" - change after login
```

---

## Post-Installation Setup

### 1. Create Company Profile
- Go to **Admin > Settings**
- Update company name, address, GSTIN, bank details
- Upload company logo

### 2. Configure Departments
- Go to **Admin > Departments**
- Add/modify departments as per your organization

### 3. Create User Roles
- Go to **Admin > Roles**
- Define roles with appropriate permissions
- Default roles are pre-configured

### 4. Add Users
- Go to **Admin > Users**
- Create user accounts for all employees
- Assign roles and departments

### 5. Configure Item Categories
- Go to **Purchase > Inventory > Categories**
- Set up material categories for your business

### 6. Add Inventory Items
- Go to **Purchase > Inventory**
- Add raw materials, consumables, hardware
- Set reorder levels and min/max stock

### 7. Add Clients and Vendors
- Go to **Clients** and **Vendors** modules
- Add your business partners

---

## Security Checklist

### Before Going Live

- [ ] Change default admin password
- [ ] Update `ENCRYPTION_KEY` in config
- [ ] Set `APP_ENV` to `production`
- [ ] Enable HTTPS (SSL certificate)
- [ ] Configure SMTP for email notifications
- [ ] Set up automated database backups
- [ ] Disable PHP error display in production
- [ ] Configure firewall rules
- [ ] Set up fail2ban for brute force protection
- [ ] Review and customize `.htaccess` rules
- [ ] Remove `seed.sql` from production server
- [ ] Set up log rotation for `/logs/` directory

### Database Security
- [ ] Use strong database password
- [ ] Restrict database access to localhost
- [ ] Enable MySQL query logging for audit
- [ ] Set up regular automated backups

### Application Security Features Already Built-in
- SQL injection protection (prepared statements)
- XSS protection (output escaping)
- CSRF token validation
- Password hashing (bcrypt)
- Session security (httponly, secure flags)
- Rate limiting on login attempts
- Account lockout after failed attempts
- Activity logging and audit trail
- Password complexity enforcement
- Secure file upload validation

---

## Directory Structure

```
/
├── config/
│   └── config.php              # Main configuration
├── core/
│   ├── Database.php            # Database class (PDO wrapper)
│   └── Controller.php          # Base controller
├── modules/
│   ├── auth/
│   │   ├── AuthController.php
│   │   └── views/
│   │       ├── login.php
│   │       ├── forgot_password.php
│   │       └── reset_password.php
│   ├── dashboard/
│   │   ├── DashboardController.php
│   │   └── views/
│   │       └── index.php
│   ├── qms/
│   │   ├── QMSController.php
│   │   └── views/
│   │       ├── index.php
│   │       ├── documents/
│   │       ├── ncr/
│   │       ├── capa/
│   │       └── ...
│   ├── projects/
│   ├── crm/
│   ├── clients/
│   ├── vendors/
│   ├── purchase/
│   ├── hr/
│   ├── accounts/
│   ├── files/
│   ├── reports/
│   └── admin/
├── templates/
│   ├── layout.php              # Main layout
│   ├── header.php              # Top navbar
│   ├── sidebar.php             # Navigation sidebar
│   └── footer.php              # Footer
├── assets/
│   ├── css/
│   │   ├── fabx-theme.css      # Main theme
│   │   └── custom.css          # Module styles
│   ├── js/
│   │   ├── fabx-app.js         # Main application JS
│   │   └── charts.js           # Chart configurations
│   └── uploads/                # File uploads directory
├── database/
│   ├── schema.sql              # Database schema
│   └── seed.sql                # Demo data
├── includes/
│   ├── functions.php           # Helper functions
│   └── security.php            # Security functions
├── logs/                       # Application logs
├── backup/                     # Backup storage
├── index.php                   # Front controller
├── .htaccess                   # Apache rewrite rules
└── INSTALL.md                  # This file
```

---

## Troubleshooting

### Common Issues

**1. 404 Error on all pages**
- Ensure `mod_rewrite` is enabled
- Check `.htaccess` is present in project root
- Verify `AllowOverride All` in Apache config

**2. Database connection error**
- Verify database credentials in `config/config.php`
- Check MySQL is running
- Ensure database user has proper privileges

**3. Session timeout too fast**
- Update `SESSION_TIMEOUT` in config (default: 1800 seconds = 30 min)
- Check PHP `session.gc_maxlifetime` setting

**4. File upload fails**
- Check `uploads/` directory is writable (chmod 775)
- Verify PHP `upload_max_filesize` and `post_max_size`
- Check file extension is in allowed list

**5. CSS/JS not loading**
- Verify `APP_URL` is correct in config
- Check browser console for 404 errors
- Ensure assets directory is accessible

**6. Charts not displaying**
- Check Chart.js CDN is accessible
- Verify no JavaScript errors in browser console

### Getting Help

- Check application logs in `/logs/` directory
- Enable debug mode temporarily: `define('APP_ENV', 'development')`
- Review Apache/Nginx error logs

---

## Backup and Recovery

### Database Backup

```bash
# Daily backup via cron
crontab -e
# Add: 0 2 * * * mysqldump -u fabx_user -p'password' fabx_erp > /backup/fabx_$(date +\%Y\%m\%d).sql
```

### File Backup

```bash
# Backup uploads directory
rsync -av /var/www/html/assets/uploads/ /backup/uploads/
```

### Recovery

```bash
# Restore database
mysql -u fabx_user -p fabx_erp < backup_file.sql

# Restore files
cp -r /backup/uploads/* /var/www/html/assets/uploads/
```

---

## License

This software is proprietary and licensed to FabX Engineering.
All rights reserved.

---

**Version**: 1.0.0
**Release Date**: 2024
**Support**: support@fabxengineering.com
