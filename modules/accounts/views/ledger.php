<?php
/**
 * FabX ERP - Accounts Ledger View
 */
?>

<div class="page-header">
    <h1 class="page-title"><i class="bi bi-journal-text text-primary"></i> Account Ledger Report</h1>
</div>

<!-- Selector Workspace -->
<div class="fx-card mb-4">
    <div class="fx-card-header"><h5 class="fx-card-title"><i class="bi bi-funnel"></i> Select Entity & Filter</h5></div>
    <div class="fx-card-body">
        <form method="GET" action="<?= base_url('accounts/ledger') ?>" id="ledgerFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="fx-form-label text-muted small d-block">Entity Type</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="entity_type" id="typeClient" value="client" <?= ($entity_type !== 'vendor') ? 'checked' : '' ?> onchange="toggleEntitySelects()">
                        <label class="btn btn-outline-secondary text-uppercase fw-bold text-white small" for="typeClient"><i class="bi bi-person-workspace me-1"></i> Client</label>

                        <input type="radio" class="btn-check" name="entity_type" id="typeVendor" value="vendor" <?= ($entity_type === 'vendor') ? 'checked' : '' ?> onchange="toggleEntitySelects()">
                        <label class="btn btn-outline-secondary text-uppercase fw-bold text-white small" for="typeVendor"><i class="bi bi-truck me-1"></i> Vendor</label>
                    </div>
                </div>
                
                <div class="col-md-6" id="clientSelectCol">
                    <label class="fx-form-label text-muted small">Select Client <span class="text-danger">*</span></label>
                    <select class="form-select select2 w-100" name="client_id" id="clientDropdown">
                        <option value="">Choose Client...</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>" <?= ($entity_type === 'client' && (int)$entity_id === (int)$client['id']) ? 'selected' : '' ?>>
                                <?= e($client['company_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 d-none" id="vendorSelectCol">
                    <label class="fx-form-label text-muted small">Select Vendor <span class="text-danger">*</span></label>
                    <select class="form-select select2 w-100" name="vendor_id" id="vendorDropdown" disabled>
                        <option value="">Choose Vendor...</option>
                        <?php foreach ($vendors as $vendor): ?>
                            <option value="<?= $vendor['id'] ?>" <?= ($entity_type === 'vendor' && (int)$entity_id === (int)$vendor['id']) ? 'selected' : '' ?>>
                                <?= e($vendor['company_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="entity_id" id="hiddenEntityId" value="<?= $entity_id ?>">

                <div class="col-md-3">
                    <button type="submit" class="btn btn-fx btn-fx-primary w-100"><i class="bi bi-journal-check"></i> Generate Statement</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Statement Display Section -->
<?php if ($entity_type && $entity_id): ?>
    <?php
    // Find current entity name
    $entityName = '';
    if ($entity_type === 'client') {
        foreach ($clients as $c) { if ((int)$c['id'] === (int)$entity_id) { $entityName = $c['company_name']; break; } }
    } else {
        foreach ($vendors as $v) { if ((int)$v['id'] === (int)$entity_id) { $entityName = $v['company_name']; break; } }
    }
    ?>
    <div class="fx-card">
        <div class="fx-card-header d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="bi bi-file-earmark-medical"></i> 
                Ledger Statement for <span class="text-primary"><?= e($entityName ?: 'Selected Entity') ?></span> 
                (<span class="text-capitalize text-muted small"><?= $entity_type ?></span>)
            </h5>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i> Print Statement</button>
        </div>
        
        <div class="fx-card-body p-0">
            <div class="table-responsive-fx">
                <table class="fx-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Txn Date</th>
                            <th>Reference No</th>
                            <th>Transaction Description</th>
                            <th class="text-end">Debits (+)</th>
                            <th class="text-end">Credits (-)</th>
                            <th class="text-end">Rolling Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($statements)): 
                            $rollingBalance = 0.00;
                            foreach ($statements as $stmt): 
                                $debit = (float)($stmt['debit'] ?? 0);
                                $credit = (float)($stmt['credit'] ?? 0);
                                $rollingBalance += ($debit - $credit);
                        ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= format_date($stmt['date']) ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-dark border border-secondary font-monospace py-2 px-3 small">
                                        <?= e($stmt['ref_no']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-semibold"><?= e($stmt['description']) ?></div>
                                    <small class="text-muted d-block text-uppercase" style="font-size:0.65rem;">Txn Type: <?= e($stmt['type']) ?></small>
                                </td>
                                <td class="text-end fw-bold text-danger">
                                    <?= $debit > 0 ? format_currency($debit) : '<span class="text-muted fw-normal">-</span>' ?>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= $credit > 0 ? format_currency($credit) : '<span class="text-muted fw-normal">-</span>' ?>
                                </td>
                                <td class="text-end fw-bold text-light-heading">
                                    <?= format_currency($rollingBalance) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        
                        <!-- Summary Footer Row -->
                        <tr class="table-dark" style="border-top: 2px solid var(--border-color);">
                            <td colspan="3" class="text-end fw-bold text-white py-3">Closing Net Outstanding Balance:</td>
                            <td colspan="2"></td>
                            <td class="text-end fw-bold text-success py-3" style="font-size: 1.1rem;">
                                <?= format_currency($rollingBalance) ?>
                            </td>
                        </tr>
                        
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-5">
                                        <i class="bi bi-journal-x display-4 mb-3 d-block text-muted"></i>
                                        <h5>No Transactional Records Found</h5>
                                        <p>No invoices raised or payment receipts registered for this entity in the system archives.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Standby state -->
    <div class="fx-card">
        <div class="fx-card-body p-5 text-center text-muted">
            <i class="bi bi-journal-text display-3 mb-3 text-secondary"></i>
            <h4>Ledger Statement standby</h4>
            <p>Please select a Client or Vendor and click "Generate Statement" to pull unified accounting chronological records.</p>
        </div>
    </div>
<?php endif; ?>

<script>
function toggleEntitySelects() {
    const isClient = document.getElementById('typeClient').checked;
    const clientCol = document.getElementById('clientSelectCol');
    const vendorCol = document.getElementById('vendorSelectCol');
    const clientDropdown = document.getElementById('clientDropdown');
    const vendorDropdown = document.getElementById('vendorDropdown');
    
    if (isClient) {
        clientCol.classList.remove('d-none');
        vendorCol.classList.add('d-none');
        clientDropdown.disabled = false;
        vendorDropdown.disabled = true;
    } else {
        clientCol.classList.add('d-none');
        vendorCol.classList.remove('d-none');
        clientDropdown.disabled = true;
        vendorDropdown.disabled = false;
    }
}

// Sync selection to hidden input before submit
document.getElementById('ledgerFilterForm').addEventListener('submit', function(e) {
    const isClient = document.getElementById('typeClient').checked;
    const hiddenId = document.getElementById('hiddenEntityId');
    if (isClient) {
        hiddenId.value = document.getElementById('clientDropdown').value;
    } else {
        hiddenId.value = document.getElementById('vendorDropdown').value;
    }
});

// Run toggle on init to match previous states
window.addEventListener('DOMContentLoaded', () => {
    toggleEntitySelects();
});
</script>
