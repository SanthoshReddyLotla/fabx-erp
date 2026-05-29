# FabX Engineering ERP

## Complete Industrial ERP + CRM + QMS System
### Built for ISO 9001 Compliant Mechanical Fabrication Companies

---

## Overview

FabX Engineering ERP is a comprehensive, enterprise-grade web application designed specifically for mechanical fabrication companies that require ISO 9001:2015 compliance. Built entirely in **Core PHP 8+** with **MySQL** and **Bootstrap 5**, it provides a modular, secure, and professional solution for managing all aspects of a fabrication business.

---

## Architecture

| Layer | Technology |
|-------|-----------|
| Backend | Core PHP 8.2+ (No frameworks) |
| Frontend | HTML5, Bootstrap 5.3, Vanilla JavaScript |
| Database | MySQL 8.0+ / MariaDB 10.5+ |
| Charts | Chart.js 4.4+ |
| Icons | Bootstrap Icons 1.11+ |
| Fonts | Google Fonts (Inter) |

### Design Patterns
- **Modular MVC-like structure** with separation of concerns
- **Singleton Database** connection with prepared statements
- **Base Controller** with shared functionality
- **Front Controller** pattern via `index.php`
- **Namespace-based autoloading** for controllers

---

## Core Modules (16 Total)

### 1. Authentication & User Management
- Secure login with bcrypt password hashing
- Session-based authentication with timeout
- Multi-role access control (12 roles)
- Department-wise permissions (8 permissions)
- Password reset with token-based security
- Account lockout after failed attempts
- User activity logs and audit trail

### 2. Dashboard
- KPI cards with real-time statistics
- Revenue trend charts
- Production analysis
- Quality metrics doughnut chart
- Project status overview
- NCR trend monitoring
- Sales pipeline visualization
- Weekly attendance tracking
- Upcoming tasks timeline
- Quick action shortcuts

### 3. ISO 9001 QMS Module
- **Document Control**: Version control, approval workflow, digital signatures, PDF export
- **NCR Management**: Severity classification, root cause analysis, corrective actions
- **CAPA**: 5-Why, Fishbone, FMEA methods, effectiveness verification
- **Internal Audits**: Audit scheduling, checklists, findings tracking
- **Calibration**: Equipment tracking, due date alerts, certificate management
- **Training Records**: Training calendars, attendance, competency matrix
- **Customer Complaints**: Investigation workflow, resolution tracking
- **Risk Assessment**: Risk matrix, probability/impact scoring, mitigation plans
- **Management Reviews**: Meeting records, action items
- **KPI & Objectives**: Quality objectives tracking, achievement metrics

### 4. Project Management
- Project creation with 9-stage pipeline
- Gantt chart visualization
- BOQ (Bill of Quantities) management
- Work order generation and tracking
- Daily production reports
- Drawing management with revision control
- Progress billing
- Machine and workforce allocation

### 5. CRM Module
- Lead management with status tracking
- Inquiry processing
- Dynamic quotation builder with GST
- Sales pipeline (Kanban view)
- Follow-up reminders
- Client communication logs

### 6. Client Management
- Client profiles with GST details
- Multiple contacts per client
- Project history
- Payment tracking
- Outstanding balances
- Support tickets
- AMC tracking
- Client portal (ready)

### 7. Vendor Management
- Vendor onboarding with approval workflow
- Vendor rating system (Quality, Delivery, Cost, Service)
- Performance evaluation history
- Compliance tracking
- Vendor portal (ready)

### 8. Purchase & Inventory
- Purchase requisitions with approval
- RFQ generation
- Purchase orders with GST
- GRN (Goods Receipt Note) management
- Incoming quality inspection
- Inventory tracking with barcode support
- Stock movement logging
- Low stock alerts
- Material issue tracking
- Scrap management

### 9. HR & Onboarding
- Employee database
- Attendance tracking
- Leave management (CL, EL, SL, ML, PL)
- Training management
- Competency matrix
- Performance appraisals
- Shift management
- ID card generation (ready)
- Document uploads

### 10. Accounts & Invoicing
- GST tax invoices
- Proforma invoices
- Payment receipts
- Expense tracking
- Vendor payments
- Ledger reports
- Outstanding reports
- P&L overview
- Multi-currency support

### 11. File Management
- Central document repository
- Folder-based organization
- Drag-drop upload support
- Drawing and CAD file management
- Revision control
- File approval workflow
- Permission-based access

### 12. Reports & Analytics
- Production reports
- Quality analysis (NCR trends, CAPA status)
- Sales reports
- Inventory status
- Financial reports
- Export to Excel, PDF, CSV

### 13. Administration
- User management
- Role & permission configuration
- Department management
- System settings
- Activity logs
- Database backup

---

## Security Features

| Feature | Implementation |
|---------|---------------|
| SQL Injection | Prepared statements (parameterized queries) |
| XSS | Output escaping with htmlspecialchars |
| CSRF | Token validation on all forms |
| Password Security | Bcrypt hashing (cost 12) |
| Session Security | HttpOnly, Secure, SameSite cookies |
| Rate Limiting | Login attempt throttling |
| Account Lockout | Auto-lock after 5 failed attempts |
| Audit Trail | Complete activity logging |
| File Upload | Extension and MIME type validation |
| Headers | X-Frame-Options, CSP, HSTS |

---

## Role-Based Access Control

### Pre-configured Roles (12)
1. Super Admin - Full system access
2. Director - Strategic oversight
3. HR Manager - HR operations
4. Production Manager - Manufacturing
5. Quality Manager - QMS operations
6. Purchase Manager - Procurement
7. Accounts - Finance operations
8. Project Manager - Project execution
9. Store Manager - Inventory
10. Operator - Limited floor access
11. Vendor - External vendor portal
12. Client - External client portal

### Permission Types (8)
- Create, Read, Update, Delete, Approve, Reject, Export, Print

---

## Database Schema

### 45+ Tables Including:
- Core: users, roles, departments, activity_logs, notifications, settings
- QMS: documents, ncr, capa, audits, calibration, training, complaints, risks, management_reviews, quality_objectives
- Projects: projects, project_stages, boq, work_orders, production_reports, drawings
- CRM: leads, quotations, quotation_items, followups
- Clients: clients, client_contacts, support_tickets, amc
- Vendors: vendors, vendor_evaluations
- Purchase: items, item_categories, purchase_requisitions, purchase_orders, grn, stock_movements, material_issues
- Accounts: invoices, invoice_items, payments, expenses, vendor_payments
- HR: employees, attendance, leaves, leave_balance, shifts, appraisals
- Files: files, folders

---

## Theme & UI

### Design System
- **Premium industrial styling** with steel-inspired aesthetics
- **Dark/Light mode** toggle with persistent preference
- **Responsive layout** - works on desktop, tablet, mobile
- **Collapsible sidebar** for maximum workspace
- **Bootstrap 5.3** with custom components
- **Chart.js** for data visualization
- **Print-optimized** layouts for documents

### Color Palette
- Primary: Steel Blue (#2c3e50, #34495e)
- Accent: Safety Orange (#e67e22, #f39c12)
- Success: Industrial Green (#27ae60)
- Danger: Alert Red (#e74c3c)
- Info: Process Blue (#3498db)

---

## Installation

See [INSTALL.md](INSTALL.md) for complete installation instructions.

Quick start:
```bash
# 1. Upload to web server
# 2. Import database/schema.sql
# 3. Import database/seed.sql (optional)
# 4. Edit config/config.php with your DB credentials
# 5. Access http://your-domain.com/fabx-erp
# 6. Login: admin@fabxengineering.com / Admin@123
```

---

## Project Structure

```
fabx-erp/
├── config/             # Configuration files
├── core/               # Database, Controller base classes
├── modules/            # All ERP modules
│   ├── auth/           # Login, logout, password reset
│   ├── dashboard/      # Main dashboard with charts
│   ├── qms/            # ISO 9001 QMS (15+ sub-modules)
│   ├── projects/       # Project management
│   ├── crm/            # Customer relationship
│   ├── clients/        # Client management
│   ├── vendors/        # Vendor management
│   ├── purchase/       # Procurement & inventory
│   ├── hr/             # Human resources
│   ├── accounts/       # Finance & invoicing
│   ├── files/          # Document repository
│   ├── reports/        # Analytics & reporting
│   └── admin/          # System administration
├── templates/          # Header, sidebar, footer, layout
├── assets/             # CSS, JS, images, uploads
├── includes/           # Helper functions, security
├── database/           # Schema and seed SQL
├── logs/               # Application logs
├── index.php           # Front controller
├── .htaccess           # Apache rewrite rules
└── INSTALL.md          # Installation guide
```

---

## License & Support

**FabX Engineering ERP v1.0.0**
- Proprietary software for FabX Engineering
- ISO 9001:2015 Compliant
- Support: support@fabxengineering.com

---

**Built with precision. Engineered for quality.**
