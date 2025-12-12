<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="<?= site_url('student/dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Materials</h6>
        </div>
        <div class="card-body">
            <?php if (empty($materials)): ?>
                <div class="alert alert-info">No materials available for this course.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="materialsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>File Type</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td><?= esc($material['file_name']) ?></td>
                                    <td><?= strtoupper(pathinfo($material['file_path'], PATHINFO_EXTENSION)) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($material['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= site_url('student/materials/download/' . $material['id']) ?>" 
                                           class="btn btn-sm btn-primary" title="Download">
                                            <i class="fas fa-download"></i> Download
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

<!-- Initialize DataTables -->
<script>
    $(document).ready(function() {
        $('#materialsTable').DataTable({
            "order": [[2, "desc"]] // Sort by upload date by default
        });
    });
</script>
<?= $this->endSection() ?>