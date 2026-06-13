-- ========================================================
-- FabX Engineering ERP - Complete Database Schema
-- MySQL 8.0+ | UTF8MB4 | InnoDB
-- ISO 9001:2015 Compliant Mechanical Fabrication ERP
-- ========================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- NOTE: On shared hosting (cPanel/Hostinger) the database is created from the
-- control panel and you import this file into it directly. Only uncomment the
-- lines below when running on a server where you can create databases yourself.
-- CREATE DATABASE IF NOT EXISTS fabx_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE fabx_erp;

-- ========================================================
-- 1. CORE & AUTHENTICATION TABLES
-- ========================================================

-- Departments
CREATE TABLE fabx_departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    cost_center VARCHAR(50),
    head_id INT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Roles & Permissions
CREATE TABLE fabx_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON NOT NULL,
    is_system TINYINT(1) DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Users
CREATE TABLE fabx_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_code VARCHAR(20) UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255),
    role_id INT NOT NULL,
    department_id INT,
    designation VARCHAR(100),
    joining_date DATE,
    reporting_to INT,
    status ENUM('active','inactive','suspended','on_leave') DEFAULT 'active',
    last_login DATETIME,
    password_changed_at DATETIME,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME,
    reset_token VARCHAR(64),
    reset_expires DATETIME,
    remember_token VARCHAR(64),
    is_deleted TINYINT(1) DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES fabx_roles(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (reporting_to) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_email (email),
    INDEX idx_role (role_id),
    INDEX idx_department (department_id),
    INDEX idx_status (status),
    INDEX idx_employee_code (employee_code)
) ENGINE=InnoDB;

-- Add foreign key for department head
ALTER TABLE fabx_departments 
ADD FOREIGN KEY (head_id) REFERENCES fabx_users(id);

-- Activity Logs (Audit Trail)
CREATE TABLE fabx_activity_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    module VARCHAR(50),
    record_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES fabx_users(id),
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE fabx_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    department VARCHAR(50),
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info','success','warning','danger') DEFAULT 'info',
    module VARCHAR(50),
    link VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES fabx_users(id),
    INDEX idx_user (user_id),
    INDEX idx_department (department),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ========================================================
-- 2. QMS / ISO 9001 TABLES
-- ========================================================

-- Document Categories
CREATE TABLE fabx_doc_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    retention_period INT, -- in months
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB;

-- Document Control (Master Document List)
CREATE TABLE fabx_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doc_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    version VARCHAR(10) DEFAULT '1.0',
    revision_no INT DEFAULT 1,
    description TEXT,
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    file_size INT,
    department_id INT,
    prepared_by INT,
    reviewed_by INT,
    approved_by INT,
    effective_date DATE,
    expiry_date DATE,
    review_date DATE,
    status ENUM('draft','under_review','approved','obsolete','superseded') DEFAULT 'draft',
    is_confidential TINYINT(1) DEFAULT 0,
    change_history JSON,
    keywords TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES fabx_doc_categories(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (prepared_by) REFERENCES fabx_users(id),
    FOREIGN KEY (reviewed_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_doc_code (doc_code),
    INDEX idx_status (status),
    INDEX idx_category (category_id),
    INDEX idx_expiry (expiry_date)
) ENGINE=InnoDB;

-- Document Distribution
CREATE TABLE fabx_doc_distribution (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT NOT NULL,
    distributed_to INT NOT NULL,
    distributed_by INT NOT NULL,
    distribution_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    acknowledgment_date DATETIME,
    status ENUM('pending','acknowledged','returned') DEFAULT 'pending',
    FOREIGN KEY (document_id) REFERENCES fabx_documents(id),
    FOREIGN KEY (distributed_to) REFERENCES fabx_users(id),
    FOREIGN KEY (distributed_by) REFERENCES fabx_users(id),
    INDEX idx_document (document_id)
) ENGINE=InnoDB;

-- Non-Conformance Reports (NCR)
CREATE TABLE fabx_ncr (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ncr_no VARCHAR(50) NOT NULL UNIQUE,
    ncr_date DATE NOT NULL,
    source ENUM('internal','external','customer_complaint','audit','incoming_inspection','inprocess_inspection','final_inspection') NOT NULL,
    project_id INT,
    department_id INT,
    reported_by INT NOT NULL,
    reported_date DATE NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('minor','major','critical') DEFAULT 'minor',
    category ENUM('material','process','documentation','equipment','personnel','system') NOT NULL,
    immediate_action TEXT,
    root_cause TEXT,
    corrective_action TEXT,
    preventive_action TEXT,
    responsible_person INT,
    target_date DATE,
    completion_date DATE,
    verification_method TEXT,
    verified_by INT,
    verification_date DATE,
    status ENUM('open','in_progress','pending_verification','closed','cancelled') DEFAULT 'open',
    attachments JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES fabx_users(id),
    FOREIGN KEY (responsible_person) REFERENCES fabx_users(id),
    FOREIGN KEY (verified_by) REFERENCES fabx_users(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    INDEX idx_ncr_no (ncr_no),
    INDEX idx_status (status),
    INDEX idx_source (source),
    INDEX idx_severity (severity)
) ENGINE=InnoDB;

-- CAPA (Corrective & Preventive Action)
CREATE TABLE fabx_capa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    capa_no VARCHAR(50) NOT NULL UNIQUE,
    source_type ENUM('ncr','audit','complaint','risk','management_review','other') NOT NULL,
    source_id INT,
    description TEXT NOT NULL,
    root_cause_analysis TEXT,
    root_cause_method ENUM('5_why','fishbone','pareto','fmea','other') DEFAULT '5_why',
    corrective_action TEXT,
    preventive_action TEXT,
    responsible_person INT,
    department_id INT,
    target_date DATE,
    implementation_date DATE,
    effectiveness_check TEXT,
    effectiveness_verified_by INT,
    effectiveness_date DATE,
    effectiveness_result ENUM('effective','partially_effective','not_effective') DEFAULT NULL,
    status ENUM('open','in_progress','implemented','verified','closed','cancelled') DEFAULT 'open',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (responsible_person) REFERENCES fabx_users(id),
    FOREIGN KEY (effectiveness_verified_by) REFERENCES fabx_users(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_capa_no (capa_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Internal Audits
CREATE TABLE fabx_audits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audit_no VARCHAR(50) NOT NULL UNIQUE,
    audit_type ENUM('internal','external','supplier','product','process') NOT NULL,
    title VARCHAR(255) NOT NULL,
    department_id INT,
    auditor_id INT NOT NULL,
    auditee_id INT,
    audit_date DATE,
    planned_start_date DATE,
    planned_end_date DATE,
    actual_start_date DATE,
    actual_end_date DATE,
    scope TEXT,
    criteria TEXT,
    checklist JSON,
    findings TEXT,
    non_conformities INT DEFAULT 0,
    observations INT DEFAULT 0,
    opportunities INT DEFAULT 0,
    audit_report TEXT,
    status ENUM('planned','in_progress','completed','cancelled','overdue') DEFAULT 'planned',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (auditor_id) REFERENCES fabx_users(id),
    FOREIGN KEY (auditee_id) REFERENCES fabx_users(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_audit_no (audit_no),
    INDEX idx_status (status),
    INDEX idx_type (audit_type)
) ENGINE=InnoDB;

-- Audit Findings
CREATE TABLE fabx_audit_findings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    audit_id INT NOT NULL,
    clause_reference VARCHAR(50),
    finding_type ENUM('major','minor','observation','opportunity') NOT NULL,
    description TEXT NOT NULL,
    evidence TEXT,
    corrective_action_required TINYINT(1) DEFAULT 0,
    capa_id INT,
    status ENUM('open','closed') DEFAULT 'open',
    FOREIGN KEY (audit_id) REFERENCES fabx_audits(id),
    FOREIGN KEY (capa_id) REFERENCES fabx_capa(id),
    INDEX idx_audit (audit_id),
    INDEX idx_type (finding_type)
) ENGINE=InnoDB;

-- Calibration Records
CREATE TABLE fabx_calibrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_id VARCHAR(50) NOT NULL,
    equipment_name VARCHAR(255) NOT NULL,
    manufacturer VARCHAR(100),
    model_no VARCHAR(100),
    serial_no VARCHAR(100),
    location VARCHAR(100),
    department_id INT,
    range_value VARCHAR(100),
    accuracy VARCHAR(50),
    frequency ENUM('monthly','quarterly','half_yearly','yearly','bi_yearly') DEFAULT 'yearly',
    last_calibration_date DATE,
    next_calibration_date DATE,
    calibration_certificate_no VARCHAR(100),
    calibrated_by VARCHAR(255),
    status ENUM('active','due','overdue','under_calibration','scrapped') DEFAULT 'active',
    remarks TEXT,
    attachments JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    INDEX idx_equipment (equipment_id),
    INDEX idx_next_date (next_calibration_date),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Training Records
CREATE TABLE fabx_training (
    id INT AUTO_INCREMENT PRIMARY KEY,
    training_code VARCHAR(50) NOT NULL UNIQUE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    training_type ENUM('induction','on_job','safety','quality','technical','soft_skill','compliance') NOT NULL,
    department_id INT,
    trainer_id INT,
    external_trainer VARCHAR(255),
    training_mode ENUM('classroom','online','on_job','workshop') DEFAULT 'classroom',
    start_date DATE,
    end_date DATE,
    duration_hours DECIMAL(5,1),
    venue VARCHAR(255),
    materials JSON,
    status ENUM('planned','ongoing','completed','cancelled') DEFAULT 'planned',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (trainer_id) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_code (training_code),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Training Participants
CREATE TABLE fabx_training_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    training_id INT NOT NULL,
    employee_id INT NOT NULL,
    attendance ENUM('present','absent','partial') DEFAULT 'present',
    score DECIMAL(5,2),
    result ENUM('pass','fail','pending') DEFAULT 'pending',
    certificate_issued TINYINT(1) DEFAULT 0,
    certificate_no VARCHAR(100),
    feedback TEXT,
    FOREIGN KEY (training_id) REFERENCES fabx_training(id),
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    INDEX idx_training (training_id),
    INDEX idx_employee (employee_id)
) ENGINE=InnoDB;

-- Competency Matrix
CREATE TABLE fabx_competency_matrix (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    skill_name VARCHAR(255) NOT NULL,
    skill_category ENUM('technical','quality','safety','computer','communication','leadership','other') NOT NULL,
    required_level ENUM('1','2','3','4','5') NOT NULL,
    actual_level ENUM('1','2','3','4','5') NOT NULL,
    gap ENUM('none','minor','significant','critical') GENERATED ALWAYS AS (
        CASE 
            WHEN CAST(actual_level AS SIGNED) >= CAST(required_level AS SIGNED) THEN 'none'
            WHEN CAST(required_level AS SIGNED) - CAST(actual_level AS SIGNED) = 1 THEN 'minor'
            WHEN CAST(required_level AS SIGNED) - CAST(actual_level AS SIGNED) = 2 THEN 'significant'
            ELSE 'critical'
        END
    ) STORED,
    last_assessed DATE,
    assessed_by INT,
    training_required TINYINT(1) DEFAULT 0,
    remarks TEXT,
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    FOREIGN KEY (assessed_by) REFERENCES fabx_users(id),
    INDEX idx_employee (employee_id),
    INDEX idx_skill (skill_name)
) ENGINE=InnoDB;

-- Customer Complaints
CREATE TABLE fabx_complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_no VARCHAR(50) NOT NULL UNIQUE,
    complaint_date DATE NOT NULL,
    client_id INT NOT NULL,
    project_id INT,
    received_by INT NOT NULL,
    source ENUM('email','phone','letter','verbal','site_visit','audit') NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('low','medium','high','critical') DEFAULT 'medium',
    immediate_action TEXT,
    investigation_findings TEXT,
    root_cause TEXT,
    corrective_action TEXT,
    preventive_action TEXT,
    capa_id INT,
    closure_date DATE,
    closed_by INT,
    customer_satisfaction ENUM('satisfied','not_satisfied','pending') DEFAULT 'pending',
    status ENUM('open','under_investigation','action_taken','verified','closed','reopened') DEFAULT 'open',
    attachments JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (received_by) REFERENCES fabx_users(id),
    FOREIGN KEY (closed_by) REFERENCES fabx_users(id),
    FOREIGN KEY (capa_id) REFERENCES fabx_capa(id),
    INDEX idx_complaint_no (complaint_no),
    INDEX idx_status (status),
    INDEX idx_client (client_id)
) ENGINE=InnoDB;

-- Management Review Meetings
CREATE TABLE fabx_management_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_no VARCHAR(50) NOT NULL UNIQUE,
    review_date DATE NOT NULL,
    chaired_by INT NOT NULL,
    attendees JSON NOT NULL,
    agenda TEXT,
    minutes TEXT,
    decisions JSON,
    action_items JSON,
    next_review_date DATE,
    status ENUM('planned','completed','cancelled') DEFAULT 'planned',
    attachments JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chaired_by) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_review_no (review_no)
) ENGINE=InnoDB;

-- Quality Objectives & KPIs
CREATE TABLE fabx_quality_objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    objective VARCHAR(255) NOT NULL,
    kpi_name VARCHAR(255) NOT NULL,
    kpi_code VARCHAR(50) NOT NULL UNIQUE,
    target_value DECIMAL(10,2),
    actual_value DECIMAL(10,2),
    unit VARCHAR(50),
    frequency ENUM('monthly','quarterly','half_yearly','yearly') DEFAULT 'monthly',
    department_id INT,
    responsible_person INT,
    year INT NOT NULL,
    month INT,
    quarter INT,
    achievement_percentage DECIMAL(5,2),
    status ENUM('on_track','at_risk','off_track','achieved') DEFAULT 'on_track',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (responsible_person) REFERENCES fabx_users(id),
    INDEX idx_kpi_code (kpi_code),
    INDEX idx_year (year),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Risk Assessment
CREATE TABLE fabx_risks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    risk_no VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    category ENUM('strategic','operational','financial','compliance','safety','quality','environmental') NOT NULL,
    department_id INT,
    probability ENUM('1','2','3','4','5') NOT NULL COMMENT '1=Very Low, 5=Very High',
    impact ENUM('1','2','3','4','5') NOT NULL COMMENT '1=Negligible, 5=Catastrophic',
    risk_score INT GENERATED ALWAYS AS (CAST(probability AS SIGNED) * CAST(impact AS SIGNED)) STORED,
    risk_level ENUM('low','medium','high','extreme') GENERATED ALWAYS AS (
        CASE 
            WHEN CAST(probability AS SIGNED) * CAST(impact AS SIGNED) <= 4 THEN 'low'
            WHEN CAST(probability AS SIGNED) * CAST(impact AS SIGNED) <= 9 THEN 'medium'
            WHEN CAST(probability AS SIGNED) * CAST(impact AS SIGNED) <= 16 THEN 'high'
            ELSE 'extreme'
        END
    ) STORED,
    mitigation_plan TEXT,
    contingency_plan TEXT,
    risk_owner INT,
    review_date DATE,
    status ENUM('open','mitigated','accepted','transferred','closed') DEFAULT 'open',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (risk_owner) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_risk_no (risk_no),
    INDEX idx_level (risk_level),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ========================================================
-- 3. PROJECT MANAGEMENT TABLES
-- ========================================================

CREATE TABLE fabx_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_code VARCHAR(50) NOT NULL UNIQUE,
    project_name VARCHAR(255) NOT NULL,
    client_id INT NOT NULL,
    description TEXT,
    project_type ENUM('fabrication','erection','both','maintenance','retrofit') NOT NULL,
    contract_value DECIMAL(15,2),
    currency ENUM('INR','USD','EUR') DEFAULT 'INR',
    start_date DATE,
    target_end_date DATE,
    actual_end_date DATE,
    project_manager_id INT,
    site_location VARCHAR(255),
    po_number VARCHAR(100),
    po_date DATE,
    po_value DECIMAL(15,2),
    advance_received DECIMAL(15,2) DEFAULT 0,
    total_billed DECIMAL(15,2) DEFAULT 0,
    total_received DECIMAL(15,2) DEFAULT 0,
    progress_percentage DECIMAL(5,2) DEFAULT 0,
    priority ENUM('low','medium','high','critical') DEFAULT 'medium',
    current_stage ENUM('planning','design','procurement','production','assembly','painting','dispatch','installation','completed','on_hold','cancelled') DEFAULT 'planning',
    status ENUM('active','completed','on_hold','cancelled','delayed') DEFAULT 'active',
    delay_reason TEXT,
    remarks TEXT,
    attachments JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (project_manager_id) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_project_code (project_code),
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_stage (current_stage)
) ENGINE=InnoDB;

-- Project Stages / Milestones
CREATE TABLE fabx_project_stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    stage_name ENUM('planning','design','procurement','production','assembly','painting','dispatch','installation') NOT NULL,
    planned_start DATE,
    planned_end DATE,
    actual_start DATE,
    actual_end DATE,
    progress_percentage DECIMAL(5,2) DEFAULT 0,
    responsible_person INT,
    remarks TEXT,
    status ENUM('pending','in_progress','completed','delayed','on_hold') DEFAULT 'pending',
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (responsible_person) REFERENCES fabx_users(id),
    INDEX idx_project (project_id)
) ENGINE=InnoDB;

-- BOQ (Bill of Quantities)
CREATE TABLE fabx_boq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    item_no VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    specification TEXT,
    uom VARCHAR(50) NOT NULL COMMENT 'Unit of Measure',
    quantity DECIMAL(12,3) NOT NULL,
    unit_rate DECIMAL(12,2),
    total_amount DECIMAL(15,2),
    material_cost DECIMAL(12,2),
    labour_cost DECIMAL(12,2),
    overhead_cost DECIMAL(12,2),
    actual_quantity DECIMAL(12,3) DEFAULT 0,
    actual_cost DECIMAL(15,2) DEFAULT 0,
    variance DECIMAL(12,3) GENERATED ALWAYS AS (actual_quantity - quantity) STORED,
    remarks TEXT,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    INDEX idx_project (project_id)
) ENGINE=InnoDB;

-- Daily Production Reports
CREATE TABLE fabx_production_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    report_date DATE NOT NULL,
    project_id INT NOT NULL,
    shift ENUM('day','night','general') DEFAULT 'day',
    work_description TEXT NOT NULL,
    manpower_used INT DEFAULT 0,
    machines_used JSON,
    progress_today TEXT,
    issues TEXT,
    tomorrow_plan TEXT,
    photos JSON,
    reported_by INT,
    approved_by INT,
    status ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (reported_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_date (report_date),
    INDEX idx_project (project_id)
) ENGINE=InnoDB;

-- Work Orders
CREATE TABLE fabx_work_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wo_no VARCHAR(50) NOT NULL UNIQUE,
    project_id INT NOT NULL,
    wo_date DATE NOT NULL,
    description TEXT NOT NULL,
    quantity DECIMAL(12,3),
    uom VARCHAR(50),
    start_date DATE,
    completion_date DATE,
    assigned_to INT,
    machine_id INT,
    estimated_hours DECIMAL(8,2),
    actual_hours DECIMAL(8,2) DEFAULT 0,
    status ENUM('pending','in_progress','completed','on_hold','cancelled') DEFAULT 'pending',
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    quality_status ENUM('pending','accepted','rejected','rework') DEFAULT 'pending',
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (assigned_to) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_wo_no (wo_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Drawings & Revisions
CREATE TABLE fabx_drawings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    drawing_no VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    revision VARCHAR(10) DEFAULT 'A',
    drawing_type ENUM('general','fabrication','assembly','detail','layout','erection','P&ID','other') NOT NULL,
    file_path VARCHAR(255),
    prepared_by INT,
    checked_by INT,
    approved_by INT,
    approval_date DATE,
    status ENUM('draft','for_check','approved','for_revision','superseded') DEFAULT 'draft',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (prepared_by) REFERENCES fabx_users(id),
    FOREIGN KEY (checked_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_project (project_id),
    INDEX idx_drawing_no (drawing_no)
) ENGINE=InnoDB;

-- ========================================================
-- 4. CRM TABLES
-- ========================================================

CREATE TABLE fabx_leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_no VARCHAR(50) NOT NULL UNIQUE,
    lead_date DATE NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    source ENUM('website','referral','exhibition','cold_call','email','social_media','direct','agent','other') NOT NULL,
    industry VARCHAR(100),
    requirements TEXT,
    estimated_value DECIMAL(15,2),
    assigned_to INT,
    priority ENUM('low','medium','high','hot') DEFAULT 'medium',
    status ENUM('new','contacted','qualified','proposal_sent','negotiation','won','lost','on_hold') DEFAULT 'new',
    next_followup DATE,
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_lead_no (lead_no),
    INDEX idx_status (status),
    INDEX idx_assigned (assigned_to)
) ENGINE=InnoDB;

-- Follow-ups
CREATE TABLE fabx_followups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT,
    client_id INT,
    followup_date DATETIME NOT NULL,
    followup_type ENUM('call','email','meeting','site_visit','other') DEFAULT 'call',
    notes TEXT,
    outcome TEXT,
    next_followup DATE,
    conducted_by INT,
    status ENUM('scheduled','completed','cancelled','overdue') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES fabx_leads(id),
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (conducted_by) REFERENCES fabx_users(id),
    INDEX idx_lead (lead_id),
    INDEX idx_date (followup_date)
) ENGINE=InnoDB;

-- Quotations
CREATE TABLE fabx_quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_no VARCHAR(50) NOT NULL UNIQUE,
    quotation_date DATE NOT NULL,
    expiry_date DATE,
    lead_id INT,
    client_id INT NOT NULL,
    contact_person VARCHAR(255),
    subject VARCHAR(255),
    description TEXT,
    terms_conditions TEXT,
    delivery_terms VARCHAR(255),
    payment_terms VARCHAR(255),
    warranty VARCHAR(255),
    validity_days INT DEFAULT 30,
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    gst_rate DECIMAL(5,2) DEFAULT 18,
    gst_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    currency ENUM('INR','USD','EUR') DEFAULT 'INR',
    prepared_by INT,
    approved_by INT,
    approval_date DATE,
    revision_no INT DEFAULT 0,
    parent_quotation_id INT,
    status ENUM('draft','sent','under_review','approved','rejected','revised','expired','accepted') DEFAULT 'draft',
    client_response TEXT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (lead_id) REFERENCES fabx_leads(id),
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (prepared_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    FOREIGN KEY (parent_quotation_id) REFERENCES fabx_quotations(id),
    INDEX idx_quotation_no (quotation_no),
    INDEX idx_client (client_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Quotation Items
CREATE TABLE fabx_quotation_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    sr_no INT NOT NULL,
    description TEXT NOT NULL,
    specification TEXT,
    quantity DECIMAL(12,3) NOT NULL,
    uom VARCHAR(50),
    unit_rate DECIMAL(12,2) NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (quotation_id) REFERENCES fabx_quotations(id),
    INDEX idx_quotation (quotation_id)
) ENGINE=InnoDB;

-- ========================================================
-- 5. CLIENT MANAGEMENT TABLES
-- ========================================================

CREATE TABLE fabx_clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_code VARCHAR(50) NOT NULL UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    alt_phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    pincode VARCHAR(20),
    gstin VARCHAR(20),
    pan VARCHAR(20),
    website VARCHAR(255),
    industry VARCHAR(100),
    credit_limit DECIMAL(15,2) DEFAULT 0,
    credit_days INT DEFAULT 30,
    payment_terms VARCHAR(255),
    client_type ENUM('direct','dealer','government','export','other') DEFAULT 'direct',
    status ENUM('active','inactive','blacklisted') DEFAULT 'active',
    portal_access TINYINT(1) DEFAULT 0,
    portal_email VARCHAR(255),
    portal_password VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_client_code (client_code),
    INDEX idx_company (company_name),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Client Contacts
CREATE TABLE fabx_client_contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    designation VARCHAR(100),
    department VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(20),
    is_primary TINYINT(1) DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    INDEX idx_client (client_id)
) ENGINE=InnoDB;

-- Support Tickets
CREATE TABLE fabx_support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_no VARCHAR(50) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    project_id INT,
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low','medium','high','critical') DEFAULT 'medium',
    category ENUM('technical','billing','quality','delivery','general') DEFAULT 'general',
    assigned_to INT,
    resolution TEXT,
    resolved_at DATETIME,
    status ENUM('open','in_progress','resolved','closed','reopened') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (assigned_to) REFERENCES fabx_users(id),
    INDEX idx_ticket_no (ticket_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- AMC (Annual Maintenance Contracts)
CREATE TABLE fabx_amc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    amc_no VARCHAR(50) NOT NULL UNIQUE,
    client_id INT NOT NULL,
    project_id INT,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    value DECIMAL(15,2),
    visit_frequency ENUM('monthly','quarterly','half_yearly','yearly','on_call') DEFAULT 'quarterly',
    total_visits INT,
    completed_visits INT DEFAULT 0,
    terms TEXT,
    status ENUM('active','expired','terminated','renewed') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_amc_no (amc_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ========================================================
-- 6. VENDOR MANAGEMENT TABLES
-- ========================================================

CREATE TABLE fabx_vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_code VARCHAR(50) NOT NULL UNIQUE,
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    alt_phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    pincode VARCHAR(20),
    gstin VARCHAR(20),
    pan VARCHAR(20),
    vendor_type ENUM('manufacturer','trader','service_provider','contractor','consultant','other') NOT NULL,
    category VARCHAR(100) COMMENT 'Material/Service category',
    credit_days INT DEFAULT 30,
    payment_terms VARCHAR(255),
    bank_name VARCHAR(255),
    bank_account_no VARCHAR(50),
    bank_ifsc VARCHAR(20),
    bank_branch VARCHAR(255),
    msme_reg_no VARCHAR(50),
    iso_certificate VARCHAR(100),
    quality_score DECIMAL(4,2) DEFAULT 0,
    delivery_score DECIMAL(4,2) DEFAULT 0,
    cost_score DECIMAL(4,2) DEFAULT 0,
    service_score DECIMAL(4,2) DEFAULT 0,
    overall_rating DECIMAL(4,2) GENERATED ALWAYS AS ((quality_score + delivery_score + cost_score + service_score) / 4) STORED,
    approval_status ENUM('pending','approved','rejected','on_hold') DEFAULT 'pending',
    approved_by INT,
    approval_date DATE,
    status ENUM('active','inactive','blacklisted') DEFAULT 'active',
    portal_access TINYINT(1) DEFAULT 0,
    documents JSON,
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_vendor_code (vendor_code),
    INDEX idx_status (status),
    INDEX idx_approval (approval_status)
) ENGINE=InnoDB;

-- Vendor Evaluation History
CREATE TABLE fabx_vendor_evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    evaluation_date DATE NOT NULL,
    evaluation_period VARCHAR(50),
    quality_score DECIMAL(4,2),
    delivery_score DECIMAL(4,2),
    cost_score DECIMAL(4,2),
    service_score DECIMAL(4,2),
    overall_score DECIMAL(4,2),
    evaluated_by INT,
    remarks TEXT,
    FOREIGN KEY (vendor_id) REFERENCES fabx_vendors(id),
    FOREIGN KEY (evaluated_by) REFERENCES fabx_users(id),
    INDEX idx_vendor (vendor_id)
) ENGINE=InnoDB;

-- ========================================================
-- 7. PURCHASE & INVENTORY TABLES
-- ========================================================

-- Item Categories
CREATE TABLE fabx_item_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    parent_id INT,
    description TEXT,
    status ENUM('active','inactive') DEFAULT 'active',
    FOREIGN KEY (parent_id) REFERENCES fabx_item_categories(id),
    INDEX idx_code (code)
) ENGINE=InnoDB;

-- Items / Materials
CREATE TABLE fabx_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    specification TEXT,
    uom VARCHAR(50) NOT NULL,
    hsn_code VARCHAR(20),
    gst_rate DECIMAL(5,2) DEFAULT 18,
    min_stock_level DECIMAL(12,3) DEFAULT 0,
    max_stock_level DECIMAL(12,3),
    reorder_level DECIMAL(12,3),
    current_stock DECIMAL(12,3) DEFAULT 0,
    avg_cost DECIMAL(12,2) DEFAULT 0,
    last_purchase_rate DECIMAL(12,2),
    barcode VARCHAR(100),
    location VARCHAR(100),
    status ENUM('active','inactive','discontinued') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES fabx_item_categories(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_item_code (item_code),
    INDEX idx_barcode (barcode),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Purchase Requisitions
CREATE TABLE fabx_purchase_requisitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pr_no VARCHAR(50) NOT NULL UNIQUE,
    pr_date DATE NOT NULL,
    department_id INT,
    project_id INT,
    required_by_date DATE,
    justification TEXT,
    requested_by INT NOT NULL,
    approved_by INT,
    approval_date DATE,
    approval_remarks TEXT,
    status ENUM('draft','submitted','approved','rejected','converted','cancelled') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (requested_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_pr_no (pr_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- PR Items
CREATE TABLE fabx_pr_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pr_id INT NOT NULL,
    item_id INT,
    description TEXT,
    quantity DECIMAL(12,3) NOT NULL,
    uom VARCHAR(50),
    required_by_date DATE,
    purpose TEXT,
    FOREIGN KEY (pr_id) REFERENCES fabx_purchase_requisitions(id),
    FOREIGN KEY (item_id) REFERENCES fabx_items(id),
    INDEX idx_pr (pr_id)
) ENGINE=InnoDB;

-- Purchase Orders
CREATE TABLE fabx_purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_no VARCHAR(50) NOT NULL UNIQUE,
    po_date DATE NOT NULL,
    pr_id INT,
    vendor_id INT NOT NULL,
    quotation_ref VARCHAR(100),
    delivery_date DATE,
    delivery_location VARCHAR(255),
    terms_conditions TEXT,
    payment_terms VARCHAR(255),
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount DECIMAL(15,2) DEFAULT 0,
    freight_amount DECIMAL(12,2) DEFAULT 0,
    gst_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    prepared_by INT,
    approved_by INT,
    approval_date DATE,
    status ENUM('draft','sent','acknowledged','partial','received','closed','cancelled') DEFAULT 'draft',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pr_id) REFERENCES fabx_purchase_requisitions(id),
    FOREIGN KEY (vendor_id) REFERENCES fabx_vendors(id),
    FOREIGN KEY (prepared_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_po_no (po_no),
    INDEX idx_vendor (vendor_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- PO Items
CREATE TABLE fabx_po_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    item_id INT,
    description TEXT NOT NULL,
    specification TEXT,
    quantity DECIMAL(12,3) NOT NULL,
    uom VARCHAR(50),
    unit_rate DECIMAL(12,2) NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    received_qty DECIMAL(12,3) DEFAULT 0,
    pending_qty DECIMAL(12,3) GENERATED ALWAYS AS (quantity - received_qty) STORED,
    FOREIGN KEY (po_id) REFERENCES fabx_purchase_orders(id),
    FOREIGN KEY (item_id) REFERENCES fabx_items(id),
    INDEX idx_po (po_id)
) ENGINE=InnoDB;

-- GRN (Goods Receipt Note)
CREATE TABLE fabx_grn (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grn_no VARCHAR(50) NOT NULL UNIQUE,
    grn_date DATE NOT NULL,
    po_id INT,
    vendor_id INT NOT NULL,
    challan_no VARCHAR(100),
    challan_date DATE,
    invoice_no VARCHAR(100),
    invoice_date DATE,
    received_by INT,
    inspected_by INT,
    inspection_result ENUM('accepted','rejected','partial','hold') DEFAULT 'hold',
    inspection_remarks TEXT,
    status ENUM('pending_inspection','accepted','rejected','partial') DEFAULT 'pending_inspection',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (po_id) REFERENCES fabx_purchase_orders(id),
    FOREIGN KEY (vendor_id) REFERENCES fabx_vendors(id),
    FOREIGN KEY (received_by) REFERENCES fabx_users(id),
    FOREIGN KEY (inspected_by) REFERENCES fabx_users(id),
    INDEX idx_grn_no (grn_no),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- GRN Items
CREATE TABLE fabx_grn_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grn_id INT NOT NULL,
    po_item_id INT,
    item_id INT,
    description TEXT,
    ordered_qty DECIMAL(12,3),
    received_qty DECIMAL(12,3),
    accepted_qty DECIMAL(12,3),
    rejected_qty DECIMAL(12,3),
    rejection_reason TEXT,
    FOREIGN KEY (grn_id) REFERENCES fabx_grn(id),
    FOREIGN KEY (item_id) REFERENCES fabx_items(id),
    INDEX idx_grn (grn_id)
) ENGINE=InnoDB;

-- Stock Movements
CREATE TABLE fabx_stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    movement_type ENUM('grn','issue','return','adjustment','transfer','scrap','opening') NOT NULL,
    reference_type VARCHAR(50),
    reference_id INT,
    quantity DECIMAL(12,3) NOT NULL,
    uom VARCHAR(50),
    unit_cost DECIMAL(12,2),
    total_cost DECIMAL(15,2),
    from_location VARCHAR(100),
    to_location VARCHAR(100),
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES fabx_items(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_item (item_id),
    INDEX idx_type (movement_type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- Material Issue
CREATE TABLE fabx_material_issues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issue_no VARCHAR(50) NOT NULL UNIQUE,
    issue_date DATE NOT NULL,
    project_id INT,
    work_order_id INT,
    issued_to INT,
    issued_by INT,
    approved_by INT,
    purpose TEXT,
    status ENUM('pending','issued','returned','partial') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (work_order_id) REFERENCES fabx_work_orders(id),
    FOREIGN KEY (issued_to) REFERENCES fabx_users(id),
    FOREIGN KEY (issued_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_issue_no (issue_no)
) ENGINE=InnoDB;

-- Material Issue Items
CREATE TABLE fabx_material_issue_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    issue_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity DECIMAL(12,3) NOT NULL,
    uom VARCHAR(50),
    returned_qty DECIMAL(12,3) DEFAULT 0,
    FOREIGN KEY (issue_id) REFERENCES fabx_material_issues(id),
    FOREIGN KEY (item_id) REFERENCES fabx_items(id),
    INDEX idx_issue (issue_id)
) ENGINE=InnoDB;

-- ========================================================
-- 8. HR & ONBOARDING TABLES
-- ========================================================

-- Employees (Extended from users)
CREATE TABLE fabx_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    employee_code VARCHAR(20) NOT NULL UNIQUE,
    date_of_birth DATE,
    gender ENUM('male','female','other'),
    marital_status ENUM('single','married','divorced','widowed'),
    blood_group VARCHAR(10),
    emergency_contact_name VARCHAR(255),
    emergency_contact_phone VARCHAR(20),
    emergency_contact_relation VARCHAR(50),
    aadhaar_no VARCHAR(20),
    pan_no VARCHAR(20),
    uan_no VARCHAR(20),
    esi_no VARCHAR(20),
    bank_name VARCHAR(255),
    bank_account_no VARCHAR(50),
    bank_ifsc VARCHAR(20),
    present_address TEXT,
    permanent_address TEXT,
    education JSON,
    experience JSON,
    skills TEXT,
    joining_date DATE,
    confirmation_date DATE,
    resignation_date DATE,
    last_working_date DATE,
    exit_reason TEXT,
    documents JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES fabx_users(id),
    INDEX idx_employee_code (employee_code)
) ENGINE=InnoDB;

-- Attendance
CREATE TABLE fabx_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('present','absent','half_day','leave','holiday','weekoff','on_duty') DEFAULT 'present',
    check_in TIME,
    check_out TIME,
    work_hours DECIMAL(4,2),
    overtime_hours DECIMAL(4,2) DEFAULT 0,
    shift ENUM('day','night','general') DEFAULT 'general',
    remarks TEXT,
    marked_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    FOREIGN KEY (marked_by) REFERENCES fabx_users(id),
    UNIQUE KEY idx_employee_date (employee_id, attendance_date),
    INDEX idx_date (attendance_date)
) ENGINE=InnoDB;

-- Leave Management
CREATE TABLE fabx_leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leave_no VARCHAR(50) NOT NULL UNIQUE,
    employee_id INT NOT NULL,
    leave_type ENUM('cl','el','sl','ml','pl','lwp','comp_off','other') NOT NULL COMMENT 'CL=Casual, EL=Earned, SL=Sick, ML=Maternity, PL=Paternity',
    from_date DATE NOT NULL,
    to_date DATE NOT NULL,
    days DECIMAL(4,1) NOT NULL,
    reason TEXT,
    contact_during_leave VARCHAR(255),
    applied_by INT,
    approved_by INT,
    approval_date DATE,
    approval_remarks TEXT,
    status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    FOREIGN KEY (applied_by) REFERENCES fabx_users(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    INDEX idx_employee (employee_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Leave Balance
CREATE TABLE fabx_leave_balance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    year INT NOT NULL,
    leave_type ENUM('cl','el','sl','ml','pl','comp_off') NOT NULL,
    entitled DECIMAL(5,1) DEFAULT 0,
    carried_forward DECIMAL(5,1) DEFAULT 0,
    availed DECIMAL(5,1) DEFAULT 0,
    balance DECIMAL(5,1) GENERATED ALWAYS AS (entitled + carried_forward - availed) STORED,
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    UNIQUE KEY idx_emp_year_type (employee_id, year, leave_type)
) ENGINE=InnoDB;

-- Shift Master
CREATE TABLE fabx_shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    grace_period INT DEFAULT 15,
    status ENUM('active','inactive') DEFAULT 'active',
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- Appraisals
CREATE TABLE fabx_appraisals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    review_period_from DATE,
    review_period_to DATE,
    reviewer_id INT,
    overall_rating DECIMAL(3,2),
    strengths TEXT,
    improvements TEXT,
    goals TEXT,
    increment_percentage DECIMAL(5,2),
    increment_amount DECIMAL(12,2),
    new_ctc DECIMAL(12,2),
    status ENUM('draft','submitted','approved','rejected') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES fabx_users(id),
    FOREIGN KEY (reviewer_id) REFERENCES fabx_users(id),
    INDEX idx_employee (employee_id)
) ENGINE=InnoDB;

-- ========================================================
-- 9. ACCOUNTS & INVOICING TABLES
-- ========================================================

-- Tax Invoices
CREATE TABLE fabx_invoices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_no VARCHAR(50) NOT NULL UNIQUE,
    invoice_date DATE NOT NULL,
    due_date DATE,
    invoice_type ENUM('tax','proforma','debit_note','credit_note') DEFAULT 'tax',
    project_id INT,
    client_id INT NOT NULL,
    po_reference VARCHAR(100),
    billing_address TEXT,
    shipping_address TEXT,
    supply_place VARCHAR(100),
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    taxable_amount DECIMAL(15,2) DEFAULT 0,
    cgst_rate DECIMAL(5,2) DEFAULT 9,
    cgst_amount DECIMAL(12,2) DEFAULT 0,
    sgst_rate DECIMAL(5,2) DEFAULT 9,
    sgst_amount DECIMAL(12,2) DEFAULT 0,
    igst_rate DECIMAL(5,2) DEFAULT 18,
    igst_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(15,2) DEFAULT 0,
    round_off DECIMAL(8,2) DEFAULT 0,
    grand_total DECIMAL(15,2) DEFAULT 0,
    amount_in_words TEXT,
    terms_conditions TEXT,
    bank_details TEXT,
    qr_code VARCHAR(255),
    digital_signature VARCHAR(255),
    status ENUM('draft','sent','paid','partial','overdue','cancelled') DEFAULT 'draft',
    paid_amount DECIMAL(15,2) DEFAULT 0,
    paid_date DATE,
    payment_mode VARCHAR(50),
    payment_reference VARCHAR(100),
    remarks TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_invoice_no (invoice_no),
    INDEX idx_client (client_id),
    INDEX idx_status (status),
    INDEX idx_due_date (due_date)
) ENGINE=InnoDB;

-- Invoice Items
CREATE TABLE fabx_invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id INT NOT NULL,
    sr_no INT,
    description TEXT NOT NULL,
    hsn_code VARCHAR(20),
    quantity DECIMAL(12,3),
    uom VARCHAR(50),
    unit_rate DECIMAL(12,2),
    amount DECIMAL(15,2),
    FOREIGN KEY (invoice_id) REFERENCES fabx_invoices(id),
    INDEX idx_invoice (invoice_id)
) ENGINE=InnoDB;

-- Payment Receipts
CREATE TABLE fabx_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_no VARCHAR(50) NOT NULL UNIQUE,
    receipt_date DATE NOT NULL,
    invoice_id INT,
    client_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    tds_amount DECIMAL(12,2) DEFAULT 0,
    net_amount DECIMAL(15,2),
    payment_mode ENUM('cash','cheque','neft','rtgs','upi','demand_draft','other') NOT NULL,
    bank_name VARCHAR(255),
    transaction_ref VARCHAR(100),
    transaction_date DATE,
    received_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (invoice_id) REFERENCES fabx_invoices(id),
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (received_by) REFERENCES fabx_users(id),
    INDEX idx_receipt_no (receipt_no),
    INDEX idx_client (client_id)
) ENGINE=InnoDB;

-- Expenses
CREATE TABLE fabx_expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_no VARCHAR(50) NOT NULL UNIQUE,
    expense_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    amount DECIMAL(12,2) NOT NULL,
    gst_amount DECIMAL(12,2) DEFAULT 0,
    total_amount DECIMAL(12,2),
    vendor VARCHAR(255),
    project_id INT,
    payment_mode VARCHAR(50),
    reference_no VARCHAR(100),
    attachments JSON,
    approved_by INT,
    status ENUM('pending','approved','rejected','paid') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (approved_by) REFERENCES fabx_users(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_expense_no (expense_no),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- Vendor Payments
CREATE TABLE fabx_vendor_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_no VARCHAR(50) NOT NULL UNIQUE,
    payment_date DATE NOT NULL,
    vendor_id INT NOT NULL,
    po_id INT,
    grn_id INT,
    invoice_ref VARCHAR(100),
    amount DECIMAL(15,2) NOT NULL,
    tds_amount DECIMAL(12,2) DEFAULT 0,
    net_amount DECIMAL(15,2),
    payment_mode ENUM('cheque','neft','rtgs','upi','cash','other') NOT NULL,
    bank_name VARCHAR(255),
    transaction_ref VARCHAR(100),
    transaction_date DATE,
    paid_by INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES fabx_vendors(id),
    FOREIGN KEY (po_id) REFERENCES fabx_purchase_orders(id),
    FOREIGN KEY (grn_id) REFERENCES fabx_grn(id),
    FOREIGN KEY (paid_by) REFERENCES fabx_users(id),
    INDEX idx_payment_no (payment_no)
) ENGINE=InnoDB;

-- Delivery Challans (goods dispatch, incl. job work / approval movements)
CREATE TABLE fabx_delivery_challans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dc_no VARCHAR(50) NOT NULL UNIQUE,
    dc_date DATE NOT NULL,
    client_id INT NOT NULL,
    project_id INT,
    invoice_id INT,
    reason ENUM('supply','job_work','sample','approval','return','others') DEFAULT 'supply',
    ship_to_address TEXT,
    vehicle_no VARCHAR(50),
    transport_mode VARCHAR(50),
    transporter VARCHAR(255),
    eway_bill_no VARCHAR(50),
    remarks TEXT,
    status ENUM('draft','dispatched','delivered','cancelled') DEFAULT 'dispatched',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES fabx_clients(id),
    FOREIGN KEY (project_id) REFERENCES fabx_projects(id),
    FOREIGN KEY (invoice_id) REFERENCES fabx_invoices(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_dc_no (dc_no),
    INDEX idx_dc_date (dc_date)
) ENGINE=InnoDB;

CREATE TABLE fabx_dc_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dc_id INT NOT NULL,
    sr_no INT NOT NULL,
    description TEXT NOT NULL,
    hsn_code VARCHAR(20),
    quantity DECIMAL(12,3) NOT NULL DEFAULT 0,
    uom VARCHAR(50) DEFAULT 'Nos',
    value DECIMAL(15,2) DEFAULT 0,
    remarks VARCHAR(255),
    FOREIGN KEY (dc_id) REFERENCES fabx_delivery_challans(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ========================================================
-- 10. FILE MANAGEMENT TABLES
-- ========================================================

CREATE TABLE fabx_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    mime_type VARCHAR(100),
    folder_id INT,
    entity_type VARCHAR(50),
    entity_id INT,
    uploaded_by INT,
    description TEXT,
    tags JSON,
    version VARCHAR(10) DEFAULT '1.0',
    is_confidential TINYINT(1) DEFAULT 0,
    download_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES fabx_users(id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_folder (folder_id)
) ENGINE=InnoDB;

-- Folders
CREATE TABLE fabx_folders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT,
    department_id INT,
    permissions JSON,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES fabx_folders(id),
    FOREIGN KEY (department_id) REFERENCES fabx_departments(id),
    FOREIGN KEY (created_by) REFERENCES fabx_users(id),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB;

-- ========================================================
-- SEED DATA
-- ========================================================

-- Default Departments
INSERT INTO fabx_departments (name, code, description) VALUES
('Management', 'MGMT', 'Company Management & Directors'),
('Human Resources', 'HR', 'HR & Administration'),
('Quality Assurance', 'QA', 'Quality Management System'),
('Production', 'PROD', 'Manufacturing & Fabrication'),
('Projects', 'PROJ', 'Project Management'),
('Sales & Marketing', 'SALES', 'Business Development & CRM'),
('Purchase', 'PUR', 'Procurement & Sourcing'),
('Stores', 'STR', 'Inventory & Warehouse'),
('Accounts', 'ACC', 'Finance & Accounts'),
('Design & Engineering', 'D&E', 'Design & Engineering'),
('Maintenance', 'MAINT', 'Equipment Maintenance'),
('Health & Safety', 'HSE', 'Health Safety Environment');

-- Default Roles with Permissions
INSERT INTO fabx_roles (name, description, permissions, is_system) VALUES
('Super Admin', 'Full system access - can do everything', 
 '["create","read","update","delete","approve","reject","export","print","admin"]', 1),
('Director', 'Company Director - Strategic oversight', 
 '["read","approve","reject","export","print"]', 1),
('HR Manager', 'Human Resources Management', 
 '["create","read","update","delete","approve","reject","export","print"]', 1),
('Production Manager', 'Production & Manufacturing oversight', 
 '["create","read","update","approve","reject","export","print"]', 1),
('Quality Manager', 'QMS, NCR, CAPA, Audits', 
 '["create","read","update","delete","approve","reject","export","print"]', 1),
('Purchase Manager', 'Procurement & Vendor management', 
 '["create","read","update","approve","reject","export","print"]', 1),
('Accounts', 'Finance, Invoicing, Payments', 
 '["create","read","update","export","print"]', 1),
('Project Manager', 'Project execution & tracking', 
 '["create","read","update","export","print"]', 1),
('Store Manager', 'Inventory & Material management', 
 '["create","read","update","export","print"]', 1),
('Operator', 'Shop floor operator - limited access', 
 '["read","create"]', 1),
('Vendor', 'External vendor portal access', 
 '["read","update"]', 1),
('Client', 'External client portal access', 
 '["read"]', 1);

-- Default Super Admin User (password: Admin@123)
INSERT INTO fabx_users (employee_code, first_name, last_name, email, phone, password, role_id, department_id, designation, joining_date, status, last_login) VALUES
('FABX001', 'System', 'Administrator', 'admin@fabxengineering.com', '9999999999',
 '$2y$12$2lsbDd3eeSgWxxyaSN26T.y7.TvjU4uYzNwL4wrCAyH6UKRVz7xzS', 1, 1, 'System Administrator', '2024-01-01', 'active', NOW());

-- Default Settings
CREATE TABLE fabx_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES fabx_users(id)
) ENGINE=InnoDB;

INSERT INTO fabx_settings (setting_key, setting_value, setting_group, description) VALUES
('company_name', 'FabX Engineering', 'company', 'Company legal name'),
('company_address', 'Industrial Estate, Manufacturing Zone, India', 'company', 'Registered address'),
('company_phone', '+91-XXXXXXXXXX', 'company', 'Primary contact number'),
('company_email', 'info@fabxengineering.com', 'company', 'Primary email'),
('company_gstin', 'XXABCXX1234X1ZX', 'company', 'GST Registration Number'),
('company_pan', 'XXXXX1234X', 'company', 'PAN Number'),
('company_bank', 'State Bank of India', 'company', 'Bank name'),
('company_account_no', '12345678901', 'company', 'Bank account number'),
('company_ifsc', 'SBIN0001234', 'company', 'Bank IFSC code'),
('financial_year_start', '04-01', 'finance', 'Financial year start month-day'),
('default_gst_rate', '18', 'finance', 'Default GST percentage'),
('currency', 'INR', 'finance', 'Default currency'),
('quotation_prefix', 'QT', 'crm', 'Quotation number prefix'),
('invoice_prefix', 'INV', 'accounts', 'Invoice number prefix'),
('po_prefix', 'PO', 'purchase', 'Purchase order prefix'),
('session_timeout', '30', 'security', 'Session idle timeout in minutes'),
('password_expiry_days', '90', 'security', 'Password expiry period'),
('theme', 'light', 'ui', 'Default UI theme'),
('iso_certificate', 'ISO 9001:2015', 'qms', 'ISO Certification'),
('certification_body', 'TUV SUD', 'qms', 'Certification Body'),
('company_state', 'Maharashtra', 'company', 'Company GST registration state (used for CGST/SGST vs IGST)'),
('gst_api_key', '', 'integrations', 'API key for GSTIN company lookup (appyflow/mastergst). Leave blank to use offline decode only.'),
('gst_api_url', 'https://appyflow.in/api/verifyGST?gstNo={GSTIN}&key_secret={KEY}', 'integrations', 'GSTIN lookup URL template with {GSTIN} and {KEY} placeholders');

-- ========================================================
-- VIEWS FOR REPORTING
-- ========================================================

-- Dashboard Summary View
CREATE VIEW vw_dashboard_summary AS
SELECT 
    (SELECT COUNT(*) FROM fabx_projects WHERE status = 'active') as active_projects,
    (SELECT COUNT(*) FROM fabx_projects WHERE status = 'delayed') as delayed_projects,
    (SELECT COUNT(*) FROM fabx_quotations WHERE status IN ('draft','sent')) as pending_quotations,
    (SELECT COUNT(*) FROM fabx_ncr WHERE status IN ('open','in_progress')) as open_ncrs,
    (SELECT COUNT(*) FROM fabx_capa WHERE status IN ('open','in_progress')) as open_capas,
    (SELECT COUNT(*) FROM fabx_purchase_requisitions WHERE status = 'pending') as pending_pr,
    (SELECT COUNT(*) FROM fabx_invoices WHERE status IN ('sent','partial','overdue')) as outstanding_invoices,
    (SELECT SUM(grand_total - paid_amount) FROM fabx_invoices WHERE status IN ('sent','partial','overdue')) as total_receivable,
    (SELECT COUNT(*) FROM fabx_calibrations WHERE next_calibration_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)) as upcoming_calibrations,
    (SELECT COUNT(*) FROM fabx_complaints WHERE status != 'closed') as open_complaints;

SET FOREIGN_KEY_CHECKS = 1;
