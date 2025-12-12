<?= $this->extend('templates/student') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?= esc($course['course_name'] ?? 'Course Materials') ?></h2>
        <a href="<?= site_url('student/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Course Materials</h6>
        </div>
        <div class="card-body">
            <?php if (empty($materials)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No materials have been uploaded for this course yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>File Name</th>
                                <th>Type</th>
                                <th>Uploaded On</th>
                                <th>Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-file-<?= $material['file_icon'] ?? 'file' ?> me-2"></i>
                                        <?= esc($material['file_name']) ?>
                                    </td>
                                    <td><?= strtoupper($material['file_type'] ?? 'N/A') ?></td>
                                    <td><?= date('M d, Y', strtotime($material['created_at'])) ?></td>
                                    <td><?= $material['file_size'] ? formatSizeUnits($material['file_size']) : 'N/A' ?></td>
                                    <td>
                                        <a href="<?= site_url('student/download/material/' . $material['id']) ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
