-- ========================================================
-- FabX ERP - Incremental upgrade for existing databases
-- Safe to run on an installation created from the original schema.
-- Import this into your existing database (phpMyAdmin / mysql CLI).
-- ========================================================

-- 1. Department cost centre (added for Admin > Master Setup).
--    If the column already exists this single statement will error
--    harmlessly - ignore "Duplicate column name 'cost_center'".
ALTER TABLE fabx_departments ADD COLUMN cost_center VARCHAR(50) NULL AFTER description;

-- 2. New settings rows (GST auto-fill + company state for GST split).
--    INSERT IGNORE is idempotent: existing keys are left untouched.
INSERT IGNORE INTO fabx_settings (setting_key, setting_value, setting_group, description) VALUES
('company_state', 'Maharashtra', 'company', 'Company GST registration state (used for CGST/SGST vs IGST)'),
('gst_api_key', '', 'integrations', 'API key for GSTIN company lookup (appyflow/mastergst). Leave blank to use offline decode only.'),
('gst_api_url', 'https://appyflow.in/api/verifyGST?gstNo={GSTIN}&key_secret={KEY}', 'integrations', 'GSTIN lookup URL template with {GSTIN} and {KEY} placeholders');

-- 3. Delivery Challan tables (Accounts > Delivery Challans).
CREATE TABLE IF NOT EXISTS fabx_delivery_challans (
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
    INDEX idx_dc_no (dc_no),
    INDEX idx_dc_date (dc_date)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS fabx_dc_items (
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
