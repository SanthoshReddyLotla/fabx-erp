<?php /** FabX ERP - Master Setup & Configuration View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-sliders"></i> Master Setup & Configuration</h1>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-4 border-secondary" id="masterSetupTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $active_tab === 'calibrations' ? 'active text-primary fw-bold' : 'text-muted' ?>" id="calibrations-tab" data-bs-toggle="tab" href="#calibrations" role="tab" aria-controls="calibrations" aria-selected="<?= $active_tab === 'calibrations' ? 'true' : 'false' ?>">
            <i class="bi bi-gear-wide-connected me-1"></i> Calibration Devices
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $active_tab === 'doc_categories' ? 'active text-primary fw-bold' : 'text-muted' ?>" id="doc_categories-tab" data-bs-toggle="tab" href="#doc_categories" role="tab" aria-controls="doc_categories" aria-selected="<?= $active_tab === 'doc_categories' ? 'true' : 'false' ?>">
            <i class="bi bi-file-earmark-lock2 me-1"></i> Document Categories
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $active_tab === 'item_categories' ? 'active text-primary fw-bold' : 'text-muted' ?>" id="item_categories-tab" data-bs-toggle="tab" href="#item_categories" role="tab" aria-controls="item_categories" aria-selected="<?= $active_tab === 'item_categories' ? 'true' : 'false' ?>">
            <i class="bi bi-box-seam me-1"></i> Inventory Categories
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link <?= $active_tab === 'departments' ? 'active text-primary fw-bold' : 'text-muted' ?>" id="departments-tab" data-bs-toggle="tab" href="#departments" role="tab" aria-controls="departments" aria-selected="<?= $active_tab === 'departments' ? 'true' : 'false' ?>">
            <i class="bi bi-diagram-3 me-1"></i> Departments & Cost Centers
        </a>
    </li>
</ul>

<div class="tab-content" id="masterSetupTabsContent">

    <!-- Tab A: Calibration & Inspection Devices -->
    <div class="tab-pane fade <?= $active_tab === 'calibrations' ? 'show active' : '' ?>" id="calibrations" role="tabpanel" aria-labelledby="calibrations-tab">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="fx-card">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0"><i class="bi bi-tools text-primary"></i> Calibration & Inspection Device Register</h5>
                    </div>
                    <div class="fx-card-body p-0">
                        <div class="table-responsive-fx">
                            <table class="fx-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Asset ID</th>
                                        <th>Device Details</th>
                                        <th>Location / Dept</th>
                                        <th>Specs & Tolerance</th>
                                        <th>Interval</th>
                                        <th>Calibration Dates</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($calibrations)): ?>
                                        <?php foreach ($calibrations as $cal): ?>
                                            <tr>
                                                <td><strong class="text-primary"><?= e($cal['equipment_id']) ?></strong></td>
                                                <td>
                                                    <div class="fw-bold text-light-heading"><?= e($cal['equipment_name']) ?></div>
                                                    <small class="text-muted">Mfg: <?= e($cal['manufacturer'] ?? '-') ?> | Model: <?= e($cal['model_no'] ?? '-') ?></small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= e($cal['location'] ?? '-') ?></div>
                                                    <small class="badge bg-dark border border-secondary text-muted"><?= e($cal['department_name'] ?? 'General') ?></small>
                                                </td>
                                                <td>
                                                    <div class="small">Range: <?= e($cal['range_value'] ?? '-') ?></div>
                                                    <div class="small text-muted">Acc: ±<?= e($cal['accuracy'] ?? '-') ?></div>
                                                </td>
                                                <td><span class="badge bg-secondary text-uppercase"><?= e(str_replace('_', ' ', $cal['frequency'])) ?></span></td>
                                                <td class="small">
                                                    <div>Last: <?= $cal['last_calibration_date'] ? format_date($cal['last_calibration_date']) : 'Never' ?></div>
                                                    <div class="text-danger">Next: <?= $cal['next_calibration_date'] ? format_date($cal['next_calibration_date']) : 'Pending' ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="?tab=calibrations&edit_type=calibration&edit_id=<?= $cal['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form method="POST" action="?tab=calibrations" onsubmit="return confirm('Are you sure you want to delete this device?');" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="action" value="delete_calibration">
                                                            <input type="hidden" name="id" value="<?= $cal['id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted p-4">No calibration devices onboarded.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="fx-card border-secondary">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle text-success"></i> 
                            <?= ($edit_type === 'calibration' && $edit_item) ? 'Edit Device' : 'Onboard Device' ?>
                        </h5>
                    </div>
                    <div class="fx-card-body p-4">
                        <form method="POST" action="?tab=calibrations" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="tab" value="calibrations">
                            <?php if ($edit_type === 'calibration' && $edit_item): ?>
                                <input type="hidden" name="action" value="update_calibration">
                                <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="action" value="create_calibration">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Asset Tag / Equipment ID</label>
                                <input type="text" name="equipment_id" class="form-control bg-dark border-secondary text-white" placeholder="e.g. EQ-001 (Auto if empty)" value="<?= e($edit_item['equipment_id'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Equipment Name</label>
                                <input type="text" name="equipment_name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Digital Vernier Caliper" required value="<?= e($edit_item['equipment_name'] ?? '') ?>">
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">Manufacturer</label>
                                    <input type="text" name="manufacturer" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Mitutoyo" value="<?= e($edit_item['manufacturer'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">Model No</label>
                                    <input type="text" name="model_no" class="form-control bg-dark border-secondary text-white" placeholder="e.g. CD-6" value="<?= e($edit_item['model_no'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">Serial No</label>
                                    <input type="text" name="serial_no" class="form-control bg-dark border-secondary text-white" placeholder="e.g. SN-98765" value="<?= e($edit_item['serial_no'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">Location</label>
                                    <input type="text" name="location" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Quality Lab" value="<?= e($edit_item['location'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">Range Value</label>
                                    <input type="text" name="range_value" class="form-control bg-dark border-secondary text-white" placeholder="e.g. 0-150mm" value="<?= e($edit_item['range_value'] ?? '') ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">Accuracy Tolerance</label>
                                    <input type="text" name="accuracy" class="form-control bg-dark border-secondary text-white" placeholder="e.g. ±0.02mm" value="<?= e($edit_item['accuracy'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">Cal Frequency</label>
                                    <select name="frequency" class="form-select bg-dark border-secondary text-white" required>
                                        <option value="monthly" <?= (isset($edit_item['frequency']) && $edit_item['frequency'] === 'monthly') ? 'selected' : '' ?>>Monthly</option>
                                        <option value="quarterly" <?= (isset($edit_item['frequency']) && $edit_item['frequency'] === 'quarterly') ? 'selected' : '' ?>>Quarterly</option>
                                        <option value="half_yearly" <?= (isset($edit_item['frequency']) && $edit_item['frequency'] === 'half_yearly') ? 'selected' : '' ?>>Half Yearly</option>
                                        <option value="yearly" <?= (!isset($edit_item['frequency']) || $edit_item['frequency'] === 'yearly') ? 'selected' : '' ?>>Yearly</option>
                                        <option value="bi_yearly" <?= (isset($edit_item['frequency']) && $edit_item['frequency'] === 'bi_yearly') ? 'selected' : '' ?>>Bi-Yearly</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">Department</label>
                                    <select name="department_id" class="form-select bg-dark border-secondary text-white">
                                        <option value="">-- General / QA --</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= (isset($edit_item['department_id']) && $edit_item['department_id'] == $dept['id']) ? 'selected' : '' ?>><?= e($dept['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small">Last Calibration Date</label>
                                    <input type="date" name="last_calibration_date" class="form-control bg-dark border-secondary text-white" value="<?= $edit_item['last_calibration_date'] ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small">Next Calibration Date</label>
                                    <input type="date" name="next_calibration_date" class="form-control bg-dark border-secondary text-white" value="<?= $edit_item['next_calibration_date'] ?? '' ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Remarks / Notes</label>
                                <textarea name="remarks" rows="2" class="form-control bg-dark border-secondary text-white" placeholder="Onboarding remarks..."><?= e($edit_item['remarks'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-fx btn-fx-primary flex-grow-1">
                                    <i class="bi bi-save"></i> <?= ($edit_type === 'calibration' && $edit_item) ? 'Save Changes' : 'Onboard Asset' ?>
                                </button>
                                <?php if ($edit_type === 'calibration' && $edit_item): ?>
                                    <a href="?tab=calibrations" class="btn btn-outline-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab B: Controlled Document Categories -->
    <div class="tab-pane fade <?= $active_tab === 'doc_categories' ? 'show active' : '' ?>" id="doc_categories" role="tabpanel" aria-labelledby="doc_categories-tab">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="fx-card">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0"><i class="bi bi-file-earmark-lock2 text-primary"></i> Controlled Document Category Directory</h5>
                    </div>
                    <div class="fx-card-body p-0">
                        <div class="table-responsive-fx">
                            <table class="fx-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Category Code</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Retention (Months)</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($doc_categories)): ?>
                                        <?php foreach ($doc_categories as $dc): ?>
                                            <tr>
                                                <td><span class="badge bg-dark border border-secondary text-light-heading font-monospace py-2 px-3"><?= e($dc['code']) ?></span></td>
                                                <td><strong><?= e($dc['name']) ?></strong></td>
                                                <td><small class="text-muted"><?= e($dc['description'] ?? '-') ?></small></td>
                                                <td><span class="fw-bold text-primary"><?= (int)$dc['retention_period'] ?> Months</span></td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="?tab=doc_categories&edit_type=doc_category&edit_id=<?= $dc['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form method="POST" action="?tab=doc_categories" onsubmit="return confirm('Are you sure you want to delete this document category?');" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="action" value="delete_doc_category">
                                                            <input type="hidden" name="id" value="<?= $dc['id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">No document categories created.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="fx-card border-secondary">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle text-success"></i> 
                            <?= ($edit_type === 'doc_category' && $edit_item) ? 'Edit Category' : 'Create Category' ?>
                        </h5>
                    </div>
                    <div class="fx-card-body p-4">
                        <form method="POST" action="?tab=doc_categories" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="tab" value="doc_categories">
                            <?php if ($edit_type === 'doc_category' && $edit_item): ?>
                                <input type="hidden" name="action" value="update_doc_category">
                                <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="action" value="create_doc_category">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Name</label>
                                <input type="text" name="name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Standard Operating Procedures" required value="<?= e($edit_item['name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Code</label>
                                <input type="text" name="code" class="form-control bg-dark border-secondary text-white" placeholder="e.g. SOP" required value="<?= e($edit_item['code'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Retention Period (Months)</label>
                                <div class="input-group">
                                    <input type="number" name="retention_period" class="form-control bg-dark border-secondary text-white" placeholder="e.g. 60" required value="<?= (int)($edit_item['retention_period'] ?? 60) ?>">
                                    <span class="input-group-text bg-dark border-secondary text-white">Months</span>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Description</label>
                                <textarea name="description" rows="3" class="form-control bg-dark border-secondary text-white" placeholder="Compliance category description..."><?= e($edit_item['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-fx btn-fx-primary flex-grow-1">
                                    <i class="bi bi-save"></i> <?= ($edit_type === 'doc_category' && $edit_item) ? 'Save Changes' : 'Create Category' ?>
                                </button>
                                <?php if ($edit_type === 'doc_category' && $edit_item): ?>
                                    <a href="?tab=doc_categories" class="btn btn-outline-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab C: Material & Inventory Categories -->
    <div class="tab-pane fade <?= $active_tab === 'item_categories' ? 'show active' : '' ?>" id="item_categories" role="tabpanel" aria-labelledby="item_categories-tab">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="fx-card">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0"><i class="bi bi-box-seam text-primary"></i> Material & Inventory Category Directory</h5>
                    </div>
                    <div class="fx-card-body p-0">
                        <div class="table-responsive-fx">
                            <table class="fx-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Category Code</th>
                                        <th>Category Name</th>
                                        <th>Parent Category</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($item_categories)): ?>
                                        <?php foreach ($item_categories as $ic): ?>
                                            <tr>
                                                <td><span class="badge bg-dark border border-secondary text-light-heading font-monospace py-2 px-3"><?= e($ic['code']) ?></span></td>
                                                <td><strong><?= e($ic['name']) ?></strong></td>
                                                <td><span class="small fw-semibold"><?= e($ic['parent_name'] ?? 'Root Category') ?></span></td>
                                                <td><small class="text-muted"><?= e($ic['description'] ?? '-') ?></small></td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="?tab=item_categories&edit_type=item_category&edit_id=<?= $ic['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form method="POST" action="?tab=item_categories" onsubmit="return confirm('Are you sure you want to delete this inventory category?');" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="action" value="delete_item_category">
                                                            <input type="hidden" name="id" value="<?= $ic['id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted p-4">No inventory categories created.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="fx-card border-secondary">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle text-success"></i> 
                            <?= ($edit_type === 'item_category' && $edit_item) ? 'Edit Inventory Category' : 'Create Inventory Category' ?>
                        </h5>
                    </div>
                    <div class="fx-card-body p-4">
                        <form method="POST" action="?tab=item_categories" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="tab" value="item_categories">
                            <?php if ($edit_type === 'item_category' && $edit_item): ?>
                                <input type="hidden" name="action" value="update_item_category">
                                <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="action" value="create_item_category">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Name</label>
                                <input type="text" name="name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Raw Plates & Sheets" required value="<?= e($edit_item['name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Code</label>
                                <input type="text" name="code" class="form-control bg-dark border-secondary text-white" placeholder="e.g. RM-STL" required value="<?= e($edit_item['code'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Parent Category (For Hierarchy)</label>
                                <select name="parent_id" class="form-select bg-dark border-secondary text-white">
                                    <option value="">-- No Parent (Root Category) --</option>
                                    <?php foreach ($item_categories as $ic): ?>
                                        <?php // Prevent selecting itself as parent
                                        if (isset($edit_item['id']) && $edit_item['id'] == $ic['id']) continue; 
                                        ?>
                                        <option value="<?= $ic['id'] ?>" <?= (isset($edit_item['parent_id']) && $edit_item['parent_id'] == $ic['id']) ? 'selected' : '' ?>><?= e($ic['name']) ?> (<?= e($ic['code']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Category Description</label>
                                <textarea name="description" rows="3" class="form-control bg-dark border-secondary text-white" placeholder="Inventory category scope description..."><?= e($edit_item['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-fx btn-fx-primary flex-grow-1">
                                    <i class="bi bi-save"></i> <?= ($edit_type === 'item_category' && $edit_item) ? 'Save Changes' : 'Create Category' ?>
                                </button>
                                <?php if ($edit_type === 'item_category' && $edit_item): ?>
                                    <a href="?tab=item_categories" class="btn btn-outline-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab D: Corporate Departments & Cost Centers -->
    <div class="tab-pane fade <?= $active_tab === 'departments' ? 'show active' : '' ?>" id="departments" role="tabpanel" aria-labelledby="departments-tab">
        <div class="row g-4">
            <div class="col-xl-8">
                <div class="fx-card">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0"><i class="bi bi-diagram-3 text-primary"></i> Corporate Department & Cost Center Directory</h5>
                    </div>
                    <div class="fx-card-body p-0">
                        <div class="table-responsive-fx">
                            <table class="fx-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Dept Code</th>
                                        <th>Department Name</th>
                                        <th>Cost Center</th>
                                        <th>Department Head</th>
                                        <th>Description</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($departments)): ?>
                                        <?php foreach ($departments as $dept): ?>
                                            <tr>
                                                <td><span class="badge bg-dark border border-secondary text-light-heading font-monospace py-2 px-3"><?= e($dept['code']) ?></span></td>
                                                <td><strong><?= e($dept['name']) ?></strong></td>
                                                <td><span class="badge bg-primary text-light font-monospace py-1 px-2"><?= e($dept['cost_center'] ?? '-') ?></span></td>
                                                <td>
                                                    <div class="fw-semibold small text-light-heading"><?= e($dept['head_name'] ?? 'Unassigned') ?></div>
                                                </td>
                                                <td><small class="text-muted"><?= e($dept['description'] ?? '-') ?></small></td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <a href="?tab=departments&edit_type=department&edit_id=<?= $dept['id'] ?>" class="btn btn-xs btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                                        <form method="POST" action="?tab=departments" onsubmit="return confirm('Are you sure you want to delete this department?');" class="d-inline">
                                                            <?= csrf_field() ?>
                                                            <input type="hidden" name="action" value="delete_department">
                                                            <input type="hidden" name="id" value="<?= $dept['id'] ?>">
                                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted p-4">No departments created.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="fx-card border-secondary">
                    <div class="fx-card-header py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle text-success"></i> 
                            <?= ($edit_type === 'department' && $edit_item) ? 'Edit Department' : 'Create Department' ?>
                        </h5>
                    </div>
                    <div class="fx-card-body p-4">
                        <form method="POST" action="?tab=departments" class="needs-validation" novalidate>
                            <?= csrf_field() ?>
                            <input type="hidden" name="tab" value="departments">
                            <?php if ($edit_type === 'department' && $edit_item): ?>
                                <input type="hidden" name="action" value="update_department">
                                <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
                            <?php else: ?>
                                <input type="hidden" name="action" value="create_department">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Department Name</label>
                                <input type="text" name="name" class="form-control bg-dark border-secondary text-white" placeholder="e.g. Fabrication Division" required value="<?= e($edit_item['name'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Department Code</label>
                                <input type="text" name="code" class="form-control bg-dark border-secondary text-white" placeholder="e.g. FAB" required value="<?= e($edit_item['code'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Cost Center Alphanumeric String</label>
                                <input type="text" name="cost_center" class="form-control bg-dark border-secondary text-white" placeholder="e.g. CC-FAB-101" required value="<?= e($edit_item['cost_center'] ?? '') ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Department Head Manager</label>
                                <select name="head_id" class="form-select bg-dark border-secondary text-white">
                                    <option value="">-- Choose Head Manager --</option>
                                    <?php foreach ($users as $usr): ?>
                                        <option value="<?= $usr['id'] ?>" <?= (isset($edit_item['head_id']) && $edit_item['head_id'] == $usr['id']) ? 'selected' : '' ?>><?= e($usr['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small">Department Description</label>
                                <textarea name="description" rows="3" class="form-control bg-dark border-secondary text-white" placeholder="Department operations scope..."><?= e($edit_item['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-fx btn-fx-primary flex-grow-1">
                                    <i class="bi bi-save"></i> <?= ($edit_type === 'department' && $edit_item) ? 'Save Changes' : 'Create Department' ?>
                                </button>
                                <?php if ($edit_type === 'department' && $edit_item): ?>
                                    <a href="?tab=departments" class="btn btn-outline-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Tab URL sync script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const tabsList = document.querySelectorAll('#masterSetupTabs a');
    tabsList.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const tabId = event.target.id.replace('-tab', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabId);
            // Remove edit parameters on tab switch to avoid loading edit mode on other tabs
            url.searchParams.delete('edit_type');
            url.searchParams.delete('edit_id');
            window.history.replaceState({}, '', url);
        });
    });
});
</script>
