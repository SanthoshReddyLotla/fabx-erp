<?php /** FabX ERP - Bill of Quantities Cost & Variance Matrix */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-list-check"></i> Bill of Quantities (BOQ) Master Cost & Variance Matrix</h1>
</div>

<!-- Filters -->
<div class="filters-bar mb-4">
    <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
        <label class="text-muted small fw-semibold me-1">Filter by Project:</label>
        <select name="project_id" class="form-select w-auto" onchange="this.form.submit()">
            <option value="">-- All Projects --</option>
            <?php foreach ($projects as $proj): ?>
                <option value="<?= $proj['id'] ?>" <?= (input('project_id') == $proj['id']) ? 'selected' : '' ?>>
                    <?= e($proj['project_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (input('project_id')): ?>
            <a href="<?= base_url('projects/boq') ?>" class="btn btn-outline-secondary btn-sm">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<!-- Master BOQ Card -->
<div class="fx-card">
    <div class="fx-card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calculator"></i> Cost Estimation vs. Actual Tracking Ledger</h5>
        <span class="badge bg-dark border border-secondary text-muted">Showing <?= count($items) ?> Records</span>
    </div>
    
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Item No</th>
                        <th>Material Description</th>
                        <th>UOM</th>
                        <th class="text-end">Est Qty</th>
                        <th class="text-end">Act Qty</th>
                        <th class="text-end">Qty Variance</th>
                        <th class="text-end">Est Rate</th>
                        <th class="text-end">Est Cost</th>
                        <th class="text-end">Mat Cost</th>
                        <th class="text-end">Lab Cost</th>
                        <th class="text-end">Ovh Cost</th>
                        <th class="text-end">Act Cost</th>
                        <th>Cost Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): 
                            $qtyVariance = (float)($item['actual_quantity'] ?? 0) - (float)$item['quantity'];
                            $estTotal = (float)($item['total_amount'] ?? ($item['quantity'] * $item['unit_rate']));
                            $actualCost = (float)($item['actual_cost'] ?? 0);
                            $costVariance = $actualCost - $estTotal;
                            
                            $qtyExceeded = $qtyVariance > 0;
                            $costExceeded = $costVariance > 0;
                            $rowClass = ($qtyExceeded || $costExceeded) ? 'bg-danger bg-opacity-10' : '';
                        ?>
                            <tr class="<?= $rowClass ?>">
                                <td>
                                    <div class="fw-bold text-light-heading"><?= e($item['project_name']) ?></div>
                                    <small class="badge bg-dark text-secondary border border-secondary" style="font-size:0.7rem;"><?= e($item['project_code']) ?></small>
                                </td>
                                <td><strong><?= e($item['item_no']) ?></strong></td>
                                <td>
                                    <div class="fw-semibold text-light-heading"><?= e($item['description']) ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 200px;"><?= e($item['specification'] ?? '-') ?></small>
                                </td>
                                <td><span class="badge bg-secondary opacity-75"><?= e($item['uom']) ?></span></td>
                                
                                <!-- Quantities -->
                                <td class="text-end fw-semibold"><?= number_format($item['quantity'], 3) ?></td>
                                <td class="text-end fw-semibold"><?= number_format($item['actual_quantity'] ?? 0, 3) ?></td>
                                <td class="text-end fw-bold <?= $qtyExceeded ? 'text-danger' : 'text-success' ?>">
                                    <?= ($qtyVariance > 0 ? '+' : '') . number_format($qtyVariance, 3) ?>
                                </td>
                                
                                <!-- Rates & Cost Breakdown -->
                                <td class="text-end text-muted small">₹<?= number_format($item['unit_rate'] ?? 0, 2) ?></td>
                                <td class="text-end fw-semibold text-primary">₹<?= number_format($estTotal, 2) ?></td>
                                <td class="text-end text-muted small">₹<?= number_format($item['material_cost'] ?? 0, 2) ?></td>
                                <td class="text-end text-muted small">₹<?= number_format($item['labour_cost'] ?? 0, 2) ?></td>
                                <td class="text-end text-muted small">₹<?= number_format($item['overhead_cost'] ?? 0, 2) ?></td>
                                
                                <!-- Actual Cost & Status -->
                                <td class="text-end fw-semibold <?= $costExceeded ? 'text-danger' : 'text-success' ?>">
                                    ₹<?= number_format($actualCost, 2) ?>
                                </td>
                                <td>
                                    <?php if ($costExceeded): ?>
                                        <span class="badge-fx badge-fx-danger d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-exclamation-triangle-fill" style="font-size:0.75rem;"></i>
                                            Cost Overrun (+₹<?= number_format($costVariance, 2) ?>)
                                        </span>
                                    <?php elseif ($qtyExceeded): ?>
                                        <span class="badge-fx badge-fx-warning d-inline-flex align-items-center gap-1 text-dark">
                                            <i class="bi bi-exclamation-circle-fill" style="font-size:0.75rem;"></i>
                                            Qty Overrun (+<?= number_format($qtyVariance, 3) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-fx badge-fx-success d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-check-circle-fill" style="font-size:0.75rem;"></i>
                                            Within Budget
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14">
                                <div class="empty-state py-5">
                                    <i class="bi bi-calculator display-4 mb-3 d-block text-muted"></i>
                                    <h5>No BOQ Cost Matrix Items Found</h5>
                                    <p>Select another project or create new BOQ items to analyze variances.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
