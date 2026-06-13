<?php
/**
 * FabX ERP - Create / Edit Client (shared form)
 * $client (array|null), $form_action, $submit_label provided by controller.
 */
$client = $client ?? null;
$isEdit = !empty($client);
$val = function (string $key, $default = '') use ($client) {
    return e((string)($client[$key] ?? $default));
};
$ctype = $client['client_type'] ?? 'direct';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-building-<?= $isEdit ? 'gear' : 'add' ?> text-primary"></i>
        <?= $isEdit ? 'Edit Client Profile' : 'Create Client Profile' ?>
    </h1>
    <a href="<?= $isEdit ? base_url('clients/view/' . $client['id']) : base_url('clients') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
</div>

<form method="POST" action="<?= $form_action ?>" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <div class="row">
        <div class="col-lg-8">
            <!-- Statutory first: GSTIN lookup drives the rest of the form -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-shield-check"></i> GST Verification &amp; Auto-Fill</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="fx-form-label text-muted small">GSTIN Number (Indian GST)</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-dark border-secondary text-white font-monospace text-uppercase" name="gstin" id="gstinInput" value="<?= $val('gstin') ?>" placeholder="e.g. 27AAPFU0939F1ZV" maxlength="15">
                                <button type="button" class="btn btn-fx btn-fx-primary" id="gstinLookupBtn"><i class="bi bi-search"></i> Verify &amp; Fetch</button>
                            </div>
                            <div id="gstinResult" class="small mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="fx-form-label text-muted small">PAN</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white font-monospace text-uppercase" name="pan" id="panInput" value="<?= $val('pan') ?>" placeholder="ABCDE1234F" maxlength="10">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Details -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-info-circle"></i> Corporate Demographics</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Company / Corporate Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="company_name" id="companyNameInput" value="<?= $val('company_name') ?>" placeholder="e.g. Reliance Industries Ltd" required>
                            <div class="invalid-feedback">Company name is required.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Primary Contact Person</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="contact_person" value="<?= $val('contact_person') ?>" placeholder="e.g. Rajesh Kumar">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Email Address</label>
                            <input type="email" class="form-control bg-dark border-secondary text-white" name="email" value="<?= $val('email') ?>" placeholder="e.g. procurement@reliance.com">
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Phone Number</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="phone" value="<?= $val('phone') ?>" placeholder="e.g. +91 22 2278 5000">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Alternate Phone Number</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="alt_phone" value="<?= $val('alt_phone') ?>" placeholder="e.g. Landline/Mobile">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Corporate Website URL</label>
                            <input type="url" class="form-control bg-dark border-secondary text-white" name="website" value="<?= $val('website') ?>" placeholder="e.g. https://www.reliance.com">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Industrial Sector</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="industry" value="<?= $val('industry') ?>" placeholder="e.g. Petrochemicals, Energy, Retail">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Client Classification Type <span class="text-danger">*</span></label>
                            <select class="form-select bg-dark border-secondary text-white" name="client_type" required>
                                <?php foreach (['direct'=>'Direct Industrial Account','dealer'=>'Distributor / Dealer Network','government'=>'Government Sector / PSU','export'=>'Export Client','other'=>'Other Classification'] as $k=>$lbl): ?>
                                    <option value="<?= $k ?>" <?= $ctype === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-cash-coin"></i> Credit Compliance</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Authorized Credit Limit (₹)</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="credit_limit" value="<?= $val('credit_limit', '500000') ?>" min="0" step="1000">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Authorized Credit Duration (Days)</label>
                            <input type="number" class="form-control bg-dark border-secondary text-white text-end" name="credit_days" value="<?= $val('credit_days', '30') ?>" min="0">
                        </div>
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Contractual Payment Terms</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="payment_terms" value="<?= $val('payment_terms') ?>" placeholder="e.g. 30 days against delivery & invoice placement">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="fx-card mb-4">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-geo-alt"></i> Address Credentials</h5></div>
                <div class="fx-card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fx-form-label text-muted small">Detailed Postal Address</label>
                            <textarea class="form-control bg-dark border-secondary text-white" name="address" id="addressInput" rows="3" placeholder="e.g. Corporate headquarters or factory location..."><?= $val('address') ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">City</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="city" id="cityInput" value="<?= $val('city') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">State</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="state" id="stateInput" value="<?= $val('state') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">Country</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="country" value="<?= $val('country', 'India') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="fx-form-label text-muted small">PIN Code</label>
                            <input type="text" class="form-control bg-dark border-secondary text-white" name="pincode" id="pincodeInput" value="<?= $val('pincode') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-fx btn-fx-primary"><i class="bi bi-check-circle"></i> <?= e($submit_label ?? 'Save') ?></button>
                <a href="<?= $isEdit ? base_url('clients/view/' . $client['id']) : base_url('clients') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>

        <!-- Tips -->
        <div class="col-lg-4">
            <div class="fx-card">
                <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-lightbulb"></i> GST Auto-Fill</h5></div>
                <div class="fx-card-body small text-muted">
                    <p class="mb-2">Enter the client's <strong class="text-white">GSTIN</strong> and click <strong class="text-white">Verify &amp; Fetch</strong>. The system instantly decodes the <strong class="text-white">state, PAN and entity type</strong> from the number and validates its check digit — no internet needed.</p>
                    <p class="mb-2">If a GST verification API key is configured under <strong class="text-white">Admin &rarr; Settings</strong>, the registered <strong class="text-white">legal name, full address, city and PIN</strong> are pulled in automatically.</p>
                    <p class="mb-0">A unique client code (prefix <code class="text-primary font-monospace">CL</code>) is generated on save.</p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    const btn = document.getElementById('gstinLookupBtn');
    const input = document.getElementById('gstinInput');
    const result = document.getElementById('gstinResult');
    const lookupUrl = <?= json_encode(base_url('clients/gstin-lookup')) ?>;
    let lastLookedUp = '';
    let debounce = null;

    function setField(id, value, onlyIfEmpty) {
        const el = document.getElementById(id);
        if (!el || value == null || value === '') return;
        if (onlyIfEmpty && el.value.trim() !== '') return;
        el.value = value;
    }

    function doLookup() {
        const gstin = (input.value || '').trim().toUpperCase();
        if (gstin.length !== 15) {
            result.innerHTML = '<span class="text-danger">GSTIN must be 15 characters.</span>';
            return;
        }
        lastLookedUp = gstin;
        btn.disabled = true;
        result.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span> Verifying…</span>';

        fetch(lookupUrl + '?gstin=' + encodeURIComponent(gstin), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                const d = res.decoded || {};
                const det = res.details || null;

                // Offline-decoded fields (always available)
                setField('panInput', d.pan, false);
                setField('stateInput', d.state, false);

                // API-fetched company details (auto-fill, don't clobber typed values)
                if (det) {
                    setField('companyNameInput', det.legal_name || det.trade_name, true);
                    setField('addressInput', det.address, true);
                    setField('cityInput', det.city, true);
                    if (det.state) setField('stateInput', det.state, false);
                    setField('pincodeInput', det.pincode, true);
                }

                let html = '';
                if (d.valid) {
                    html += '<span class="text-success"><i class="bi bi-check-circle-fill"></i> ' + (res.message || 'Verified') + '</span>';
                } else {
                    html += '<span class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i> ' + (res.message || 'Check the number') + '</span>';
                }
                const chips = [];
                if (d.state) chips.push('State: <strong>' + d.state + '</strong>');
                if (d.entity_type) chips.push('Type: <strong>' + d.entity_type + '</strong>');
                if (det && det.status) chips.push('Status: <strong>' + det.status + '</strong>');
                if (det && det.registered_on) chips.push('Reg: <strong>' + det.registered_on + '</strong>');
                if (chips.length) html += '<div class="text-muted mt-1">' + chips.join(' &middot; ') + '</div>';
                result.innerHTML = html;
            })
            .catch(() => {
                btn.disabled = false;
                result.innerHTML = '<span class="text-danger">Lookup failed. Please try again.</span>';
            });
    }

    btn.addEventListener('click', doLookup);

    // Auto-fetch as soon as a full 15-char GSTIN is typed/pasted, so the form
    // fills itself without the user clicking anything.
    input.addEventListener('input', function () {
        const gstin = (input.value || '').trim().toUpperCase();
        clearTimeout(debounce);
        if (gstin.length === 15 && gstin !== lastLookedUp) {
            debounce = setTimeout(doLookup, 400);
        }
    });
})();
</script>
