<?php
/**
 * FabX ERP - Create Vendor View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-truck text-primary"></i> Create Vendor Profile</h1>
    <a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Directory</a>
</div>

<form method="POST" action="<?= base_url('vendors/create') ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <!-- Basic Details Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Corporate Demographics</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Company / Supplier Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="company_name" placeholder="e.g. Tata Steel Ltd" required>
                            <div class="invalid-feedback">Company name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Primary Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="contact_person" placeholder="e.g. Ramesh Patil" required>
                            <div class="invalid-feedback">Contact person name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control bg-dark border-secondary text-white" name="email" placeholder="e.g. sales@tatasteel.com" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="phone" placeholder="e.g. +91 22 6665 8282" required>
                            <div class="invalid-feedback">Phone number is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Vendor Classification Type <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="vendor_type" required>
                                <option value="manufacturer">Manufacturer</option>
                                <option value="trader">Trader / Distributor</option>
                                <option value="service_provider">Service Provider</option>
                                <option value="contractor">Subcontractor</option>
                                <option value="consultant">Consultant</option>
                                <option value="other">Other</option>
                            </select>
                            <div class="invalid-feedback">Please select vendor type.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Material / Service Category <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="category" placeholder="e.g. Raw Materials, Fasteners, Tools" required>
                            <div class="invalid-feedback">Please enter material/service category.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax compliance & credit terms -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-shield-check"></i> Statutory & Credit Compliance</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">GSTIN Number (Indian GST)</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white font-monospace text-uppercase" name="gstin" placeholder="e.g. 27AAAAA1111A1Z1" max="15">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Permanent Account Number (PAN)</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white font-monospace text-uppercase" name="pan" placeholder="e.g. ABCDE1234F" max="10">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Default Credit Period (Days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="credit_days" value="30" min="0" required>
                            <div class="invalid-feedback">Credit days are required.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details Card -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-bank"></i> Settlement & Bank Account Credentials</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Bank Name</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="bank_name" placeholder="e.g. State Bank of India">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Account Number</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white font-monospace" name="bank_account_no" placeholder="e.g. 123456789012">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">IFSC Code</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white font-monospace text-uppercase" name="bank_ifsc" placeholder="e.g. SBIN0001234" max="11">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address credentials -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-geo-alt"></i> Address Credentials</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Detailed Postal Address <span class="text-danger">*</span></label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="address" rows="3" placeholder="e.g. Warehouse or Factory Head Office address..." required></textarea>
                            <div class="invalid-feedback">Postal address is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="city" required>
                            <div class="invalid-feedback">City is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="state" required>
                            <div class="invalid-feedback">State is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="country" value="India" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="pincode" required>
                            <div class="invalid-feedback">PIN Code is required.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Onboard Vendor</button>
                <a href="<?= base_url('vendors') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Sidebar Guidelines -->
        <div class="col-lg-4">
            <div class="fx-card">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-lightbulb"></i> Onboarding Rules</h5></div>
                <div class="fx-card-body small text-muted">
                    <p class="mb-2"><strong class="text-white">Approval Protocol:</strong> On creation, the vendor status is registered as <span class="badge bg-warning text-dark">Pending</span>. The Quality or Procurement Manager must authorize the vendor profiles before PO orders can be issued.</p>
                    <p class="mb-2"><strong class="text-white">MSME and ISO compliance:</strong> Verification of GSTIN/PAN and payment systems allows automated tax splitting and vendor invoice matching in Accounts.</p>
                </div>
            </div>
        </div>
    </div>
</form>
