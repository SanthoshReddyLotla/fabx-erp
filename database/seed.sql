-- ========================================================
-- FabX ERP - Seed Data
-- Sample data for testing and demonstration
-- ========================================================

-- NOTE: Import this into the same database you imported schema.sql into.
-- Only uncomment when the database is literally named fabx_erp.
-- USE fabx_erp;

-- Document Categories
INSERT INTO fabx_doc_categories (name, code, description, retention_period) VALUES
('Quality Manual', 'QM', 'ISO 9001 Quality Manual and related documents', 60),
('Procedures', 'PROC', 'Standard Operating Procedures', 60),
('Work Instructions', 'WI', 'Detailed work instructions for shop floor', 36),
('Forms', 'FORM', 'Standard forms and formats', 36),
('External Documents', 'EXT', 'Customer drawings, standards, codes', 24),
('Inspection Reports', 'INSP', 'Incoming, in-process and final inspection reports', 60),
('Test Reports', 'TEST', 'Material test certificates and lab reports', 60),
('Management Review', 'MR', 'Management review meeting records', 60),
('Training Records', 'TR', 'Training calendars, attendance, competency records', 60),
('Audit Records', 'AUD', 'Internal and external audit reports', 60),
('Calibration Records', 'CAL', 'Equipment calibration certificates and schedules', 60),
('NCR/CAPA', 'NC', 'Non-conformance and corrective action records', 60),
('Project Documents', 'PRJ', 'Project-specific quality documents', 36);

-- Item Categories
INSERT INTO fabx_item_categories (name, code, description) VALUES
('Raw Materials - Steel', 'RM-STL', 'Steel plates, sheets, sections, bars'),
('Raw Materials - Stainless Steel', 'RM-SS', 'SS plates, sheets, pipes, fittings'),
('Raw Materials - Aluminum', 'RM-AL', 'Aluminum sheets, sections, pipes'),
('Consumables - Welding', 'CON-WLD', 'Electrodes, wires, gases, flux'),
('Consumables - Cutting', 'CON-CUT', 'Cutting discs, grinding wheels, nozzles'),
('Consumables - Painting', 'CON-PNT', 'Primers, paints, thinners, brushes'),
('Hardware - Fasteners', 'HW-FST', 'Bolts, nuts, washers, screws'),
('Hardware - Gaskets', 'HW-GSK', 'Rubber, PTFE, spiral wound gaskets'),
('Tools - Hand Tools', 'TL-HND', 'Wrenches, hammers, screwdrivers'),
('Tools - Power Tools', 'TL-PWR', 'Grinders, drills, cutting machines'),
('PPE', 'PPE', 'Safety helmets, gloves, shoes, goggles'),
('Electrical', 'ELEC', 'Cables, switches, motors, panels'),
('Pipes & Fittings', 'PIPE', 'MS pipes, SS pipes, flanges, elbows'),
('Valves', 'VAL', 'Gate valves, globe valves, check valves');

-- Sample Items
INSERT INTO fabx_items (item_code, name, description, category_id, specification, uom, hsn_code, gst_rate, min_stock_level, reorder_level, current_stock, location) VALUES
('STL-PLT-001', 'MS Plate 6mm', 'Mild Steel Hot Rolled Plate 6mm thk', 1, 'IS 2062 E250BR, 6mm x 2500 x 6000mm', 'MT', '7208', 18, 5, 10, 25, 'RM Store A'),
('STL-PLT-002', 'MS Plate 10mm', 'Mild Steel Hot Rolled Plate 10mm thk', 1, 'IS 2062 E250BR, 10mm x 2500 x 6000mm', 'MT', '7208', 18, 3, 8, 18, 'RM Store A'),
('STL-PLT-003', 'MS Plate 12mm', 'Mild Steel Hot Rolled Plate 12mm thk', 1, 'IS 2062 E250BR, 12mm x 2500 x 6000mm', 'MT', '7208', 18, 2, 5, 12, 'RM Store A'),
('SS-PLT-001', 'SS 304 Plate 3mm', 'Stainless Steel 304 Plate 3mm', 2, 'ASTM A240 TP304, 3mm x 1500 x 3000mm, 2B Finish', 'MT', '7219', 18, 2, 5, 8, 'RM Store B'),
('SS-PLT-002', 'SS 316 Plate 5mm', 'Stainless Steel 316 Plate 5mm', 2, 'ASTM A240 TP316, 5mm x 1500 x 3000mm, 2B Finish', 'MT', '7219', 18, 1, 3, 5, 'RM Store B'),
('WLD-ELC-001', 'Welding Electrode E6013', 'MS Welding Electrode 3.15mm x 350mm', 4, 'E6013, AWS A5.1, 3.15mm x 350mm', 'KG', '8311', 18, 50, 100, 200, 'Consumables Store'),
('WLD-WIR-001', 'MIG Wire ER70S-6', 'Mild Steel MIG Welding Wire', 4, 'ER70S-6, 1.2mm dia, 15kg spool', 'KG', '8311', 18, 30, 60, 120, 'Consumables Store'),
('CUT-DSC-001', 'Cutting Disc 4"', 'Abrasive cutting disc for mild steel', 5, '4" x 1/8" x 5/8", Type 41', 'Nos', '6804', 18, 50, 100, 250, 'Consumables Store'),
('PNT-PRM-001', 'Red Oxide Primer', 'Metal primer for steel surfaces', 6, 'Synthetic red oxide primer, 20L drum', 'LTR', '3208', 18, 20, 40, 80, 'Paint Store'),
('FST-BLT-001', 'Hex Bolt M16x50', 'High Tensile Hex Bolt Gr 8.8', 7, 'M16 x 50mm, Gr 8.8, IS 1364, Zinc Plated', 'Nos', '7318', 18, 100, 200, 500, 'Hardware Store'),
('FST-BLT-002', 'Hex Bolt M20x70', 'High Tensile Hex Bolt Gr 8.8', 7, 'M20 x 70mm, Gr 8.8, IS 1364, Zinc Plated', 'Nos', '7318', 18, 50, 100, 250, 'Hardware Store'),
('GSK-RUB-001', 'Rubber Gasket 3mm', 'Nitrile Rubber Gasket Sheet', 8, 'NBR, 3mm thick, 60 Shore A, Oil Resistant', 'MT', '3926', 18, 5, 10, 20, 'Hardware Store'),
('PPE-GLV-001', 'Leather Welding Gloves', 'Heavy duty welding gloves', 11, 'Chrome leather, 14", Kevlar stitched', 'Pair', '4203', 18, 20, 40, 60, 'PPE Store'),
('PPE-HLT-001', 'Safety Helmet Yellow', 'Industrial safety helmet', 11, 'HDPE, IS 2925, Ratchet adjustment, Yellow', 'Nos', '6506', 18, 10, 20, 35, 'PPE Store'),
('PIPE-MS-001', 'MS Pipe SCH40 2"', 'Mild Steel Seamless Pipe', 13, '2" NB, SCH40, ASTM A106 Gr B, 6m length', 'MT', '7304', 18, 5, 10, 15, 'RM Store A'),
('PIPE-MS-002', 'MS Pipe SCH40 4"', 'Mild Steel Seamless Pipe', 13, '4" NB, SCH40, ASTM A106 Gr B, 6m length', 'MT', '7304', 18, 3, 8, 10, 'RM Store A');

-- Sample Clients
INSERT INTO fabx_clients (client_code, company_name, contact_person, email, phone, address, city, state, country, pincode, gstin, pan, industry, credit_limit, credit_days, client_type, status) VALUES
('CL-001', 'Reliance Industries Ltd', 'Mr. Rajesh Kumar', 'procurement@ril.com', '9876543210', 'Maker Chambers IV, Nariman Point', 'Mumbai', 'Maharashtra', 'India', '400021', '27AABCR1718E1ZP', 'AABCR1718E', 'Oil & Gas', 50000000, 60, 'direct', 'active'),
('CL-002', 'Tata Steel Limited', 'Ms. Priya Sharma', 'purchase@tatasteel.com', '9876543211', 'Jamshedpur Works', 'Jamshedpur', 'Jharkhand', 'India', '831001', '20AAACT1115Q1Z5', 'AAACT1115Q', 'Steel Manufacturing', 25000000, 45, 'direct', 'active'),
('CL-003', 'Larsen & Toubro Ltd', 'Mr. Suresh Menon', 'buying@lt.com', '9876543212', 'L&T House, Ballard Estate', 'Mumbai', 'Maharashtra', 'India', '400001', '27AAACL0141A1ZL', 'AAACL0141A', 'Construction', 30000000, 30, 'direct', 'active'),
('CL-004', 'Indian Oil Corporation', 'Mr. Anil Gupta', 'materials@iocl.co.in', '9876543213', 'G-9, Ali Yavar Jung Marg', 'Mumbai', 'Maharashtra', 'India', '400023', '27AAACI1681G1ZP', 'AAACI1681G', 'Oil & Gas', 40000000, 60, 'government', 'active'),
('CL-005', 'Bharat Petroleum', 'Ms. Sunita Reddy', 'purchase@bharatpetroleum.in', '9876543214', 'Bharat Bhavan, 4 & 6 Currimbhoy Road', 'Mumbai', 'Maharashtra', 'India', '400021', '27AAACB1531F1ZP', 'AAACB1531F', 'Oil & Gas', 35000000, 45, 'government', 'active');

-- Sample Vendors
INSERT INTO fabx_vendors (vendor_code, company_name, contact_person, email, phone, address, city, state, country, pincode, gstin, pan, vendor_type, category, credit_days, approval_status, status) VALUES
('VN-001', 'SAIL Distribution Company', 'Mr. Ramesh Patel', 'sales@saildc.com', '9876543220', 'Ispat Bhavan, Lodi Road', 'New Delhi', 'Delhi', 'India', '110003', '07AAECS1234F1Z5', 'AAECS1234F', 'manufacturer', 'Steel Plates & Sheets', 30, 'approved', 'active'),
('VN-002', 'Jindal Stainless Ltd', 'Ms. Kavita Jain', 'marketing@jindalstainless.com', '9876543221', 'Jindal Centre, 12 Bhikaji Cama Place', 'New Delhi', 'Delhi', 'India', '110066', '07AAACJ1234H1Z2', 'AAACJ1234H', 'manufacturer', 'Stainless Steel Products', 30, 'approved', 'active'),
('VN-003', 'ESAB India Ltd', 'Mr. Deepak Singh', 'india@esab.com', '9876543222', 'ESAB House, Plot 25, Sector 44', 'Gurgaon', 'Haryana', 'India', '122003', '06AAACE1234N1Z8', 'AAACE1234N', 'manufacturer', 'Welding Consumables', 30, 'approved', 'active'),
('VN-004', 'BASF India Ltd', 'Mr. Prakash Iyer', 'contact@basf.com', '9876543223', 'The Capital, Plot C-70, G Block', 'Mumbai', 'Maharashtra', 'India', '400013', '27AABCB1234M1ZE', 'AABCB1234M', 'manufacturer', 'Industrial Paints & Coatings', 45, 'approved', 'active'),
('VN-005', 'Sundram Fasteners Ltd', 'Mr. Karthik Sundaram', 'exports@sundram.com', '9876543224', '98-A, Developed Plots', 'Chennai', 'Tamil Nadu', 'India', '600096', '33AABCS1234L1ZK', 'AABCS1234L', 'manufacturer', 'Fasteners & Gaskets', 30, 'approved', 'active');

-- Sample Projects
INSERT INTO fabx_projects (project_code, project_name, client_id, description, project_type, contract_value, currency, start_date, target_end_date, project_manager_id, site_location, po_number, po_date, po_value, advance_received, progress_percentage, current_stage, status) VALUES
('PRJ-2024-001', 'Storage Tank Fabrication - 500 KL', 1, 'Design, fabrication, supply and erection of 500 KL capacity API 650 storage tank', 'both', 2850000, 'INR', '2024-01-15', '2024-06-30', 1, 'Reliance Jamnagar Refinery, Gujarat', 'RIL/PRO/2024/001', '2024-01-10', 2850000, 855000, 65, 'production', 'active'),
('PRJ-2024-002', 'Pressure Vessel - Separator Drum', 2, 'Fabrication of 3-phase separator vessel as per ASME Sec VIII Div 1', 'fabrication', 4200000, 'INR', '2024-02-01', '2024-08-15', 1, 'Tata Steel Jamshedpur', 'TS/MECH/2024/045', '2024-01-25', 4200000, 1260000, 40, 'procurement', 'active'),
('PRJ-2024-003', 'Pipe Spooling Package', 3, 'Prefabrication of CS pipe spools for utility piping system', 'fabrication', 1850000, 'INR', '2024-03-01', '2024-05-30', 1, 'L&T Hazira, Surat', 'LT-P-2024-1287', '2024-02-20', 1850000, 555000, 85, 'painting', 'active'),
('PRJ-2024-004', 'Heat Exchanger Shell & Tube', 4, 'Design and fabrication of shell and tube heat exchanger, TEMA BEU', 'both', 6500000, 'INR', '2024-03-15', '2024-10-31', 1, 'IOCL Paradip Refinery, Odisha', 'IOCL/PAR/2024/MECH/089', '2024-03-01', 6500000, 1950000, 25, 'design', 'active'),
('PRJ-2024-005', 'Ducting & HVAC Fabrication', 5, 'Supply and installation of GI ducting for process ventilation', 'both', 950000, 'INR', '2024-04-01', '2024-06-15', 1, 'BPCL Kochi Refinery, Kerala', 'BPCL/KR/2024/0892', '2024-03-20', 950000, 285000, 15, 'planning', 'active');

-- Sample NCRs
INSERT INTO fabx_ncr (ncr_no, ncr_date, source, project_id, reported_by, reported_date, description, severity, category, status) VALUES
('NCR-2024-A1B2', '2024-03-10', 'incoming_inspection', 2, 1, '2024-03-10', 'Plate thickness 9.2mm measured against specified 10mm for vessel shell plates. MTC indicates 10mm but actual measurement shows 9.2mm.', 'major', 'material', 'open'),
('NCR-2024-C3D4', '2024-03-15', 'inprocess_inspection', 1, 1, '2024-03-15', 'Weld profile on tank bottom plate - excessive reinforcement (4mm against max 3mm as per WPS). WPS No: WPS-2024-012.', 'minor', 'process', 'in_progress'),
('NCR-2024-E5F6', '2024-03-20', 'final_inspection', 3, 1, '2024-03-20', 'Dimensional check shows spool SP-045 length 2980mm against drawing dimension 3000mm. Tolerance +/- 2mm exceeded.', 'major', 'process', 'open'),
('NCR-2024-G7H8', '2024-02-28', 'audit', NULL, 1, '2024-02-28', 'Internal audit finding: WPS qualification record for procedure WPS-2024-008 not available. PQR reference missing.', 'major', 'documentation', 'in_progress');

-- Sample CAPAs
INSERT INTO fabx_capa (capa_no, source_type, source_id, description, root_cause_analysis, root_cause_method, corrective_action, preventive_action, responsible_person, target_date, status, created_by) VALUES
('CAPA-2024-A1B2', 'ncr', 1, 'Incoming plate thickness non-conformance', 'Supplier quality control gap. Ultrasonic thickness gauge at supplier end was not calibrated.', '5_why', 'Reject non-conforming batch. Request replacement from supplier.', 'Implement incoming inspection checklist with mandatory thickness verification. Calibrate UT gauge monthly.', 1, '2024-04-15', 'open', 1),
('CAPA-2024-C3D4', 'ncr', 2, 'Weld reinforcement excess', 'Welder not following WPS parameters. Travel speed too slow.', '5_why', 'Re-grind weld to acceptable profile. Re-train welder on WPS compliance.', 'Implement weld visual inspection after each pass. Use weld gauges.', 1, '2024-04-01', 'in_progress', 1);

-- Sample Calibrations
INSERT INTO fabx_calibrations (equipment_id, equipment_name, manufacturer, model_no, serial_no, location, department_id, range_value, accuracy, frequency, last_calibration_date, next_calibration_date, calibrated_by, status) VALUES
('CAL-001', 'Vernier Caliper 300mm', 'Mitutoyo', 'CD-300', 'VC2024001', 'QC Lab', 3, '0-300mm', '0.02mm', 'yearly', '2023-12-15', '2024-12-15', 'NABL Lab, Mumbai', 'active'),
('CAL-002', 'Ultrasonic Thickness Gauge', 'Olympus', '38DL PLUS', 'UT2024001', 'Incoming Inspection', 3, '0.5-500mm', '0.01mm', 'yearly', '2024-01-10', '2025-01-10', 'NABL Lab, Mumbai', 'active'),
('CAL-003', 'Welding Machine - MIG', 'Lincoln Electric', 'Power Wave 450', 'WM2024001', 'Shop Floor A', 4, '50-450A', '1%', 'quarterly', '2024-02-01', '2024-05-01', 'Internal', 'active'),
('CAL-004', 'Torque Wrench 1/2"', 'Norbar', 'Pro 100', 'TW2024001', 'Assembly', 4, '10-100 Nm', '2%', 'half_yearly', '2024-01-20', '2024-07-20', 'NABL Lab, Pune', 'active'),
('CAL-005', 'Pressure Gauge 0-10 bar', 'WIKA', '213.53', 'PG2024001', 'Hydrotest Area', 4, '0-10 bar', '0.5% FS', 'yearly', '2024-02-15', '2025-02-15', 'NABL Lab, Mumbai', 'active');

-- Sample Quotations
INSERT INTO fabx_quotations (quotation_no, quotation_date, client_id, subject, description, terms_conditions, delivery_terms, payment_terms, validity_days, subtotal, gst_rate, gst_amount, total_amount, prepared_by, status) VALUES
('QT-202403-A1B2', '2024-03-01', 1, 'Quotation for Tank Fabrication Works', 'Fabrication of 2 nos 250 KL storage tanks complete with internals, nozzles, and accessories', '1. Validity: 30 days\n2. Taxes: GST extra @ 18%\n3. Packing: Standard seaworthy', '8-10 weeks ex-works', '30% advance, 60% against dispatch, 10% after erection', 30, 4500000, 18, 810000, 5310000, 1, 'sent'),
('QT-202403-C3D4', '2024-03-10', 3, 'Quotation for Pressure Vessel', 'Design, fabrication and testing of 1 no. pressure vessel as per ASME Sec VIII Div 1', '1. Design approval required before fabrication\n2. Third party inspection by TUV\n3. Datasheets to be provided by client', '12-14 weeks ex-works', '30% advance, 40% progressive, 30% against dispatch', 30, 3200000, 18, 576000, 3776000, 1, 'approved'),
('QT-202403-E5F6', '2024-03-15', 4, 'Structural Steel Fabrication', 'Supply and fabrication of structural steel platform and staircase', '1. Rate valid for 15 days\n2. Scope as per BOQ attached\n3. Erection scope separate', '6-8 weeks ex-works', '50% advance, 50% against delivery', 15, 850000, 18, 153000, 1003000, 1, 'draft');

-- Sample Purchase Requisitions
INSERT INTO fabx_purchase_requisitions (pr_no, pr_date, department_id, required_by_date, justification, requested_by, status) VALUES
('PR-20240318-A1B2', '2024-03-18', 4, '2024-04-05', 'Required for PRJ-2024-004 Heat Exchanger fabrication. Material as per project BOQ.', 1, 'submitted'),
('PR-20240320-C3D4', '2024-03-20', 4, '2024-04-10', 'Required for PRJ-2024-001 tank shell courses. Urgent - production schedule at risk.', 1, 'approved');

-- Insert PR Items
INSERT INTO fabx_pr_items (pr_id, item_id, description, quantity, uom, required_by_date, purpose) VALUES
(1, 2, 'MS Plate 10mm IS 2062 E250BR for tube sheets', 5, 'MT', '2024-04-05', 'Heat Exchanger tube sheets'),
(1, 15, 'MS Pipe SCH40 2" for shell construction', 3, 'MT', '2024-04-05', 'Heat Exchanger shell'),
(2, 1, 'MS Plate 6mm for tank roof and bottom plates', 12, 'MT', '2024-04-10', 'Storage Tank 500 KL');

-- Activity Logs
INSERT INTO fabx_activity_logs (user_id, action, description, module, ip_address, created_at) VALUES
(1, 'LOGIN', 'User logged in successfully', 'auth', '127.0.0.1', NOW()),
(1, 'PROJECT_CREATED', 'Project PRJ-2024-001 created', 'projects', '127.0.0.1', '2024-01-15 10:30:00'),
(1, 'QUOTATION_CREATED', 'Quotation QT-202403-ABC1 created for Reliance', 'crm', '127.0.0.1', '2024-03-01 14:20:00'),
(1, 'NCR_CREATED', 'NCR NCR-2024-A1B2 created for incoming plate', 'qms', '127.0.0.1', '2024-03-10 09:15:00'),
(1, 'INVOICE_CREATED', 'Invoice INV-202403-A1B2 created', 'accounts', '127.0.0.1', '2024-03-20 16:45:00');
