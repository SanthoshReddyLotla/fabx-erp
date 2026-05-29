<?php /** FabX ERP - File Repository View */ ?>
<div class="page-header">
    <h1 class="page-title"><i class="bi bi-folder2-open"></i> Document Repository</h1>
    <div class="page-actions">
        <button class="btn btn-fx btn-fx-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="bi bi-cloud-upload"></i> Upload File
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Folders Sidebar -->
    <div class="col-md-3">
        <div class="fx-card">
            <div class="fx-card-header"><strong><i class="bi bi-folder"></i> Folders</strong></div>
            <div class="fx-card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item <?= !input('folder') ? 'active' : '' ?>">
                        <a href="<?= base_url('files') ?>" class="text-decoration-none <?= !input('folder') ? 'text-white' : '' ?>">
                            <i class="bi bi-house"></i> All Files
                        </a>
                    </li>
                    <?php foreach ($folders as $folder): ?>
                        <li class="list-group-item <?= input('folder') == $folder['id'] ? 'active' : '' ?>">
                            <a href="?folder=<?= $folder['id'] ?>" class="text-decoration-none <?= input('folder') == $folder['id'] ? 'text-white' : '' ?>">
                                <i class="bi bi-folder"></i> <?= e($folder['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Files List -->
    <div class="col-md-9">
        <div class="fx-card">
            <div class="fx-card-header d-flex justify-content-between align-items-center">
                <strong>Files</strong>
                <small class="text-muted">Storage used: <?= round(($total_size ?? 0) / 1024 / 1024, 2) ?> MB</small>
            </div>
            <div class="fx-card-body p-0">
                <div class="table-responsive-fx">
                    <table class="fx-table mb-0">
                        <thead>
                            <tr>
                                <th>File Name</th><th>Type</th><th>Size</th>
                                <th>Uploaded By</th><th>Date</th><th class="actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($files)): ?>
                                <?php foreach ($files as $file): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-file-earmark-text me-1 text-primary"></i>
                                            <?= e($file['original_name'] ?? $file['file_name'] ?? '-') ?>
                                        </td>
                                        <td><span class="badge bg-light text-dark"><?= strtoupper(e($file['file_type'] ?? '-')) ?></span></td>
                                        <td><?= round(($file['file_size'] ?? 0) / 1024, 1) ?> KB</td>
                                        <td><?= e($file['uploaded_by_name'] ?? '-') ?></td>
                                        <td><?= format_date($file['created_at']) ?></td>
                                        <td class="actions">
                                            <a href="<?= base_url('assets/uploads/' . $file['file_path']) ?>" target="_blank" class="btn btn-sm btn-light" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-folder2-open"></i>
                                        <h5>No files found</h5>
                                        <p>Upload files to this folder to get started.</p>
                                    </div>
                                </td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
