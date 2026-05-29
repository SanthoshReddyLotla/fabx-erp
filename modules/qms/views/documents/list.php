<?php
/**
 * QMS - Document Control List View
 */
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="bi bi-files"></i>
        Document Control
    </h1>
    <div class="page-actions">
        <a href="<?= base_url('qms/documents/create') ?>" class="btn btn-fx btn-fx-primary">
            <i class="bi bi-plus-lg"></i> New Document
        </a>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <div class="flex-grow-1">
        <div class="input-group">
            <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="Search documents..." name="search" value="<?= e($filters['search'] ?? '') ?>">
        </div>
    </div>
    <div>
        <select class="form-select" name="category" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($filters['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= e($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <select class="form-select" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="under_review" <?= ($filters['status'] ?? '') === 'under_review' ? 'selected' : '' ?>>Under Review</option>
            <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
            <option value="obsolete" <?= ($filters['status'] ?? '') === 'obsolete' ? 'selected' : '' ?>>Obsolete</option>
        </select>
    </div>
    <button class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer"></i>
    </button>
    <button class="btn btn-outline-secondary" onclick="exportTableToCSV('documentsTable', 'documents_<?= date('Y-m-d') ?>.csv')">
        <i class="bi bi-download"></i> Export
    </button>
</div>

<!-- Documents Table -->
<div class="fx-card">
    <div class="fx-card-body p-0">
        <div class="table-responsive-fx">
            <table class="fx-table mb-0" id="documentsTable">
                <thead>
                    <tr>
                        <th>Doc Code</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Version</th>
                        <th>Department</th>
                        <th>Prepared By</th>
                        <th>Effective Date</th>
                        <th>Status</th>
                        <th class="actions">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td><strong><?= e($doc['doc_code']) ?></strong></td>
                                <td><?= e($doc['title']) ?></td>
                                <td><?= e($doc['category_name'] ?? '-') ?></td>
                                <td><span class="badge bg-light text-dark border"><?= e($doc['version']) ?></span></td>
                                <td><?= e($doc['department_id'] ? 'Dept ' . $doc['department_id'] : '-') ?></td>
                                <td><?= e($doc['prepared_by_name'] ?? '-') ?></td>
                                <td><?= format_date($doc['effective_date']) ?></td>
                                <td>
                                    <?php
                                    $statusClass = match($doc['status']) {
                                        'approved' => 'badge-fx-success',
                                        'under_review' => 'badge-fx-warning',
                                        'draft' => 'badge-fx-secondary',
                                        'obsolete' => 'badge-fx-danger',
                                        default => 'badge-fx-secondary'
                                    };
                                    ?>
                                    <span class="badge-fx <?= $statusClass ?>">
                                        <?= ucfirst(str_replace('_', ' ', $doc['status'])) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="<?= base_url('qms/documents/view/' . $doc['id']) ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= base_url('qms/documents/edit/' . $doc['id']) ?>" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-sm btn-light" data-bs-toggle="tooltip" title="Print" onclick="printDocument('<?= $doc['doc_code'] ?>')">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-files"></i>
                                    <h5>No documents found</h5>
                                    <p class="mb-3">Start by creating your first controlled document.</p>
                                    <a href="<?= base_url('qms/documents/create') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-lg"></i> Create Document
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="fx-card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing <?= $pagination['offset'] + 1 ?> - <?= min($pagination['offset'] + $pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> documents</small>
                <nav>
                    <div class="pagination-fx">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?= $pagination['page'] - 1 ?>">&laquo;</a>
                        <?php endif; ?>
                        <?php for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++): ?>
                            <a href="?page=<?= $i ?>" class="<?= $i === $pagination['page'] ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?= $pagination['page'] + 1 ?>">&raquo;</a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function printDocument(docCode) {
    window.open(`<?= base_url('qms/documents/print/') ?>${docCode}`, '_blank');
}
</script>
