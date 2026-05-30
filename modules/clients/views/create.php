<?php
/**
 * FabX ERP - Create Client View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-building-add text-primary"></i> Create Client Profile</h1>
    <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Directory</a>
</div>

<form method="POST" action="<?= base_url('clients/create') ?>" class="needs-validation" novalidate>
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
                            <label class="fx-form-label text-muted small">Company / Corporate Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="company_name" placeholder="e.g. Reliance Industries Ltd" required>
                            <div class="invalid-feedback">Company name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Primary Contact Person <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="contact_person" placeholder="e.g. Rajesh Kumar" required>
                            <div class="invalid-feedback">Contact person name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control bg-dark border-secondary text-white" name="email" placeholder="e.g. procurement@reliance.com" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="phone" placeholder="e.g. +91 22 2278 5000" required>
                            <div class="invalid-feedback">Phone number is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Alternate Phone Number</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="alt_phone" placeholder="e.g. Landline/Mobile">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Website URL</label>
                            <input type="url" class="form-control bg-dark border-secondary text-white" name="website" placeholder="e.g. https://www.reliance.com">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Industrial Sector</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="industry" placeholder="e.g. Petrochemicals, Energy, Retail">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Client Classification Type <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_type" required>
                                <option value="direct">Direct Industrial Account</option>
                                <option value="dealer">Distributor / Dealer Network</option>
                                <option value="government">Government Sector / PSU</option>
                                <option value="export">Export Client</option>
                                <option value="other">Other Classification</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax compliance & credit safeguards -->
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
                            <label class="fx-form-label text-muted small">Authorized Credit Limit (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="credit_limit" value="500000" min="0" step="1000" required>
                            <div class="invalid-feedback">Credit limit is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Authorized Credit Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="credit_days" value="30" min="0" required>
                            <div class="invalid-feedback">Credit days are required.</div>
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Contractual Payment Terms Description</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="payment_terms" placeholder="e.g. 30 days against delivery & invoice placement">
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
                            <textarea class="form-control bg-dark border-secondary text-white" name="address" rows="3" placeholder="e.g. Corporate headquarters or factory location..." required></textarea>
                            <div class="invalid-feedback">Postal address is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="city" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="state" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="country" value="India" required>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="pincode" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> Create Client Profile</button>
                <a href="<?= base_url('clients') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Sidebar / Tips Column -->
        <div class="col-lg-4">
            <div class="fx-card">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-lightbulb"></i> Onboarding Guidelines</h5></div>
                <div class="fx-card-body small text-muted">
                    <p class="mb-2"><strong class="text-white">Alphanumeric Code Generation:</strong> On submission, the system will automatically generate a unique, serialized tracking identification code starting with the prefix <code class="text-primary font-monospace">CL</code>.</p>
                    <p class="mb-2"><strong class="text-white">Credit Compliance Safeguards:</strong> Enforcing realistic credit limit and credit days will prevent invoicing overruns and block unauthorized transactions dynamically during procurement.</p>
                    <p class="mb-0"><strong class="text-white">Statutory Numbers:</strong> Ensure GSTIN matches the Indian tax schema to allow Intrastate/Interstate splitting on generated invoices.</p>
                </div>
            </div>
        </div>
    </div>
</form>
