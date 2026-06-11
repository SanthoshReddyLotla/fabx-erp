<?php /** FabX ERP - Create Project View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-folder-plus"></i> Create New Project</h1>
    <div class="page-actions">
        <a href="<?= base_url('projects') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="fx-card">
            <div class="fx-card-header py-3">
                <h5 class="mb-0"><i class="bi bi-sliders"></i> Project Specifications & Commercial Details</h5>
            </div>
            
            <div class="fx-card-body p-4">
                <form method="POST" action="<?= base_url('projects/create') ?>" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    
                    <!-- Basic Information -->
                    <h6 class="fw-bold mb-3 text-light-heading"><i class="bi bi-info-circle"></i> Basic Specifications</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Project Name</label>
                            <input type="text" name="project_name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Structural Steel Fabrication - Phase 2" required>
                            <div class="invalid-feedback">Please enter the project name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Client / Customer</label>
                            <select name="client_id" class="form-select bg-dark border-secondary text-white" required>
                                <option value="">-- Select Client --</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= $client['id'] ?>"><?= e($client['company_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a client.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Project Type</label>
                            <select name="project_type" class="form-select bg-dark border-secondary text-white" required>
                                <option value="fabrication">Fabrication</option>
                                <option value="installation">Installation</option>
                                <option value="assembly">Assembly</option>
                                <option value="design_and_drafting">Design & Drafting</option>
                                <option value="turnkey">Turnkey Project</option>
                            </select>
                            <div class="invalid-feedback">Please select a project type.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Project Manager</label>
                            <select name="project_manager_id" class="form-select bg-dark border-secondary text-white" required>
                                <option value="">-- Select Manager --</option>
                                <?php foreach ($managers as $mgr): ?>
                                    <option value="<?= $mgr['id'] ?>"><?= e($mgr['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please assign a project manager.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Project Description / Scope of Work</label>
                            <textarea name="description" rows="3" class="form-control bg-dark border-secondary text-white" placeholder="Provide details about the fabrication scope, specifications, and client requirements..."></textarea>
                        </div>
                    </div>
                    
                    <hr class="border-secondary my-4">
                    
                    <!-- Commercials & Location -->
                    <h6 class="fw-bold mb-3 text-light-heading"><i class="bi bi-cash-stack"></i> Commercials & Site Parameters</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label text-muted small">Contract Value (INR)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary text-white">₹</span>
                                <input type="number" step="0.01" name="contract_value" class="form-control bg-dark border-secondary text-white" placeholder="0.00" required>
                                <div class="invalid-feedback">Please enter the contract value.</div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-muted small">Site / Delivery Location</label>
                            <input type="text" name="site_location" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Sector 5, Industrial Area, Pune" required>
                            <div class="invalid-feedback">Please specify the delivery site location.</div>
                        </div>
                    </div>
                    
                    <hr class="border-secondary my-4">
                    
                    <!-- Purchase Order Details -->
                    <h6 class="fw-bold mb-3 text-light-heading"><i class="bi bi-file-earmark-text"></i> Purchase Order (PO) Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">PO Number</label>
                            <input type="text" name="po_number" class="form-control bg-dark border-secondary text-white" placeholder="e.g. PO-2024-8976" required>
                            <div class="invalid-feedback">Please enter the purchase order number.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">PO Date</label>
                            <input type="date" name="po_date" data-datepicker="po" class="form-control bg-dark border-secondary text-white" required>
                            <div class="invalid-feedback">Please select the PO date.</div>
                        </div>
                    </div>
                    
                    <hr class="border-secondary my-4">
                    
                    <!-- Timelines -->
                    <h6 class="fw-bold mb-3 text-light-heading"><i class="bi bi-calendar3"></i> Schedule & Delivery Timelines</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Scheduled Start Date</label>
                            <input type="date" name="start_date" data-datepicker="start" class="form-control bg-dark border-secondary text-white" required>
                            <div class="invalid-feedback">Please select the start date.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Target End Date</label>
                            <input type="date" name="target_end_date" data-datepicker="end" class="form-control bg-dark border-secondary text-white" required>
                            <div class="invalid-feedback">Please select the target completion date.</div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-3 justify-content-end mt-2">
                        <a href="<?= base_url('projects') ?>" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-fx btn-fx-primary">
                            <i class="bi bi-check-circle"></i> Initialize Project & Milestones
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
