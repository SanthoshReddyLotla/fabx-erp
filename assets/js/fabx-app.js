/**
 * FabX Engineering ERP - Main Application JavaScript
 * Vanilla JS, Bootstrap 5 compatible
 */

(function () {
    'use strict';

    const FabX = {
        // Configuration
        config: {
            sessionTimeout: 30 * 60 * 1000, // 30 minutes
            heartbeatInterval: 5 * 60 * 1000, // 5 minutes
            apiBase: '/fabx-erp/api/'
        },

        // State
        state: {
            sidebarCollapsed: localStorage.getItem('fabx_sidebar') === 'true',
            theme: localStorage.getItem('fabx_theme') || 'light',
            sessionTimer: null,
            heartbeatTimer: null
        },

        /**
         * Initialize the application
         */
        init: function () {
            this.initSidebar();
            this.initTheme();
            this.initSessionTimer();
            this.initEventListeners();
            this.initTooltips();
            this.initSelect2();
            this.initFlatpickr();
            this.initDataTables();
            this.initConfirmDialogs();
        },

        /**
         * Sidebar toggle and submenu handling
         */
        initSidebar: function () {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            const mobileToggle = document.getElementById('mobileSidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');
            const wrapper = document.querySelector('.main-wrapper');

            if (!sidebar || !wrapper) return;

            // Single toggle button — behaves differently on desktop vs mobile
            if (toggle) {
                toggle.addEventListener('click', () => {
                    if (window.innerWidth >= 992) {
                        // Desktop: collapse/expand sidebar
                        document.body.classList.toggle('sidebar-collapsed');
                        wrapper.classList.toggle('sidebar-collapsed');
                        this.state.sidebarCollapsed = document.body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('fabx_sidebar', this.state.sidebarCollapsed);
                    } else {
                        // Mobile: open/close sidebar as a drawer
                        sidebar.classList.toggle('mobile-open');
                        overlay?.classList.toggle('active');
                    }
                });
            }

            // Mobile close button (X) inside the sidebar brand area
            if (mobileToggle) {
                mobileToggle.addEventListener('click', () => {
                    sidebar.classList.remove('mobile-open');
                    overlay?.classList.remove('active');
                });
            }

            // Overlay click — close mobile sidebar
            if (overlay) {
                overlay.addEventListener('click', () => {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                });
            }

            // Submenu toggles — use event delegation on the nav to avoid
            // Bootstrap intercepting clicks on individual <a href="#"> elements.
            const sidebarNav = sidebar.querySelector('.sidebar-nav');
            if (sidebarNav) {
                sidebarNav.addEventListener('click', (e) => {
                    const toggleLink = e.target.closest('.submenu-toggle');
                    if (!toggleLink) return;
                    e.preventDefault();
                    e.stopPropagation();
                    const parent = toggleLink.closest('.nav-item');
                    const submenu = parent ? parent.querySelector('.submenu') : null;
                    if (parent) parent.classList.toggle('open');
                    if (submenu) submenu.classList.toggle('show');
                });
            }

            // Apply saved collapsed state on page load (desktop only)
            if (this.state.sidebarCollapsed && window.innerWidth >= 992) {
                document.body.classList.add('sidebar-collapsed');
                wrapper.classList.add('sidebar-collapsed');
            }
        },

        /**
         * Theme toggle (light/dark)
         */
        initTheme: function () {
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;

            // Apply saved theme
            html.setAttribute('data-bs-theme', this.state.theme);

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const current = html.getAttribute('data-bs-theme');
                    const next = current === 'light' ? 'dark' : 'light';

                    html.setAttribute('data-bs-theme', next);
                    this.state.theme = next;
                    localStorage.setItem('fabx_theme', next);

                    // Update icon
                    const icon = themeToggle.querySelector('i');
                    if (icon) {
                        icon.className = next === 'light' ? 'bi bi-moon-stars' : 'bi bi-sun';
                    }

                    // Sync to server
                    this.ajax('/auth/toggle-theme', 'POST');
                });
            }
        },

        /**
         * Session timeout timer
         */
        initSessionTimer: function () {
            const timerEl = document.getElementById('timerDisplay');
            if (!timerEl) return;

            let remaining = this.config.sessionTimeout;

            const updateTimer = () => {
                const minutes = Math.floor(remaining / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);
                timerEl.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                if (remaining <= 60000) {
                    timerEl.style.color = 'var(--danger)';
                }

                if (remaining <= 0) {
                    clearInterval(this.state.sessionTimer);
                    Swal.fire({
                        title: 'Session Expired',
                        text: 'Your session has expired. Please login again.',
                        icon: 'warning',
                        confirmButtonText: 'Login',
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = '/fabx-erp/auth/login?timeout=1';
                    });
                    return;
                }

                remaining -= 1000;
            };

            this.state.sessionTimer = setInterval(updateTimer, 1000);
            updateTimer();

            // Reset timer on activity
            ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
                document.addEventListener(event, () => {
                    remaining = this.config.sessionTimeout;
                    timerEl.style.color = '';
                }, { passive: true });
            });

            // Heartbeat to keep session alive
            this.state.heartbeatTimer = setInterval(() => {
                this.ajax('/auth/heartbeat', 'POST')
                    .then(response => {
                        if (!response.success) {
                            window.location.reload();
                        }
                    })
                    .catch(() => {
                        // Silent fail
                    });
            }, this.config.heartbeatInterval);
        },

        /**
         * Global event listeners
         */
        initEventListeners: function () {
            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + K for search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    document.getElementById('globalSearch')?.focus();
                }

                // Escape to close modals
                if (e.key === 'Escape') {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const modal = bootstrap.Modal.getInstance(openModal);
                        modal?.hide();
                    }
                }
            });

            // Scroll to top button
            const scrollTop = document.querySelector('.scroll-top');
            if (scrollTop) {
                window.addEventListener('scroll', () => {
                    scrollTop.classList.toggle('visible', window.scrollY > 300);
                });
                scrollTop.addEventListener('click', () => {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            // Form validation
            document.querySelectorAll('form[data-validate]').forEach(form => {
                form.addEventListener('submit', (e) => {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });

            // Auto-dismiss alerts
            document.querySelectorAll('.alert-dismissible').forEach(alert => {
                setTimeout(() => {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert?.close();
                }, 5000);
            });

            // Dropdown hover for desktop (optional)
            if (window.innerWidth > 992) {
                document.querySelectorAll('.dropdown-hover').forEach(dropdown => {
                    dropdown.addEventListener('mouseenter', () => {
                        dropdown.querySelector('.dropdown-toggle')?.click();
                    });
                    dropdown.addEventListener('mouseleave', () => {
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu?.classList.contains('show')) {
                            dropdown.querySelector('.dropdown-toggle')?.click();
                        }
                    });
                });
            }
        },

        /**
         * Initialize Bootstrap tooltips & popovers
         */
        initTooltips: function () {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                new bootstrap.Popover(el);
            });
        },

        /**
         * Initialize Select2
         */
        initSelect2: function () {
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                jQuery('.select2').select2({
                    theme: 'default',
                    width: '100%',
                    placeholder: 'Select an option'
                });
            }
        },

        /**
         * Initialize Flatpickr date pickers
         */
        initFlatpickr: function () {
            if (typeof flatpickr !== 'undefined') {
                document.querySelectorAll('[data-datepicker]').forEach(el => {
                    flatpickr(el, {
                        dateFormat: 'd-m-Y',
                        allowInput: true
                    });
                });

                document.querySelectorAll('[data-datetimepicker]').forEach(el => {
                    flatpickr(el, {
                        dateFormat: 'd-m-Y H:i',
                        enableTime: true,
                        allowInput: true
                    });
                });
            }
        },

        /**
         * Initialize DataTables
         */
        initDataTables: function () {
            // Simple table enhancements without DataTables library
            document.querySelectorAll('.table-sortable').forEach(table => {
                const headers = table.querySelectorAll('thead th[data-sort]');
                headers.forEach(header => {
                    header.style.cursor = 'pointer';
                    header.addEventListener('click', () => {
                        const column = header.dataset.sort;
                        this.sortTable(table, column, header);
                    });
                });
            });
        },

        /**
         * Simple table sort
         */
        sortTable: function (table, column, header) {
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const isAsc = !header.classList.contains('sort-asc');

            // Remove sort classes
            table.querySelectorAll('th').forEach(th => {
                th.classList.remove('sort-asc', 'sort-desc');
            });

            rows.sort((a, b) => {
                const aVal = a.querySelector(`[data-sort="${column}"]`)?.textContent?.trim() || '';
                const bVal = b.querySelector(`[data-sort="${column}"]`)?.textContent?.trim() || '';

                const aNum = parseFloat(aVal.replace(/[^0-9.-]/g, ''));
                const bNum = parseFloat(bVal.replace(/[^0-9.-]/g, ''));

                if (!isNaN(aNum) && !isNaN(bNum)) {
                    return isAsc ? aNum - bNum : bNum - aNum;
                }

                return isAsc ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            });

            header.classList.add(isAsc ? 'sort-asc' : 'sort-desc');
            rows.forEach(row => tbody.appendChild(row));
        },

        /**
         * Confirm dialog replacements
         */
        initConfirmDialogs: function () {
            document.querySelectorAll('[data-confirm]').forEach(el => {
                el.addEventListener('click', (e) => {
                    const message = el.dataset.confirm;
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

            // SweetAlert2 confirmations
            document.querySelectorAll('[data-swal-confirm]').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.preventDefault();
                    const config = {
                        title: 'Are you sure?',
                        text: el.dataset.swalConfirm || 'This action cannot be undone.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--danger)',
                        cancelButtonColor: 'var(--secondary)',
                        confirmButtonText: 'Yes, proceed',
                        cancelButtonText: 'Cancel'
                    };

                    Swal.fire(config).then(result => {
                        if (result.isConfirmed) {
                            const form = el.closest('form');
                            if (form) {
                                form.submit();
                            } else if (el.href) {
                                window.location.href = el.href;
                            }
                        }
                    });
                });
            });
        },

        /**
         * AJAX helper
         */
        ajax: function (url, method = 'GET', data = null, options = {}) {
            const defaults = {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrfToken) {
                defaults.headers['X-CSRF-Token'] = csrfToken;
            }

            const config = { ...defaults, ...options };
            config.headers = { ...defaults.headers, ...options.headers };

            const fetchOptions = {
                method: method,
                headers: config.headers,
                credentials: config.credentials
            };

            if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
                if (data instanceof FormData) {
                    fetchOptions.body = data;
                } else {
                    fetchOptions.headers['Content-Type'] = 'application/json';
                    fetchOptions.body = JSON.stringify(data);
                }
            }

            return fetch(url, fetchOptions).then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                return response.text();
            });
        },

        /**
         * Show toast notification
         */
        toast: function (message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            } else {
                // Fallback
                alert(message);
            }
        },

        /**
         * Format currency
         */
        formatCurrency: function (amount, symbol = true) {
            const formatted = parseFloat(amount || 0).toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            return symbol ? '₹ ' + formatted : formatted;
        },

        /**
         * Format date
         */
        formatDate: function (date, format = 'DD-MM-YYYY') {
            if (!date) return '-';
            const d = new Date(date);
            if (isNaN(d.getTime())) return date;

            const pad = (n) => n.toString().padStart(2, '0');
            return format
                .replace('DD', pad(d.getDate()))
                .replace('MM', pad(d.getMonth() + 1))
                .replace('YYYY', d.getFullYear());
        },

        /**
         * Debounce function
         */
        debounce: function (func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Mark notification as read
         */
        markNotificationRead: function (id) {
            this.ajax('/api/notifications/' + id + '/read', 'POST')
                .then(() => {
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        badge.textContent = count > 0 ? count : '';
                        if (count <= 0) badge.style.display = 'none';
                    }
                });
        }
    };

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FabX.init());
    } else {
        FabX.init();
    }

    // Expose globally
    window.FabX = FabX;

})();

// Global utility functions
function markAllRead() {
    FabX.ajax('/api/notifications/mark-all-read', 'POST')
        .then(() => {
            document.querySelector('.notification-badge')?.remove();
            document.querySelectorAll('.notification-item.unread').forEach(el => {
                el.classList.remove('unread');
            });
        });
}

// Print function
function printPage() {
    window.print();
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        cols.forEach(col => {
            let data = col.textContent.replace(/"/g, '""').trim();
            rowData.push('"' + data + '"');
        });
        csv.push(rowData.join(','));
    });

    const csvContent = '\uFEFF' + csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename || 'export.csv';
    link.click();
    URL.revokeObjectURL(link.href);
}
