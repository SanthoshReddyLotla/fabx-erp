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
