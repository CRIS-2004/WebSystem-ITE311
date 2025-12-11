<?= $this->extend('templates/student') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <!-- Debug Info - Remove after testing -->
    <div class="alert alert-info d-none">
        <h5>Debug Info</h5>
        <pre><?= htmlspecialchars(print_r([
            'course' => $course,
            'materials_count' => $materials ? count($materials) : 0,
            'session' => session()->get()
        ], true)) ?></pre>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Course Materials - <?= esc($course['course_name'] ?? 'Unknown Course') ?></h2>
        <a href="<?= site_url('student/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Available Materials</h6>
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
                                <th>Uploaded On</th>
                                <th>Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): 
                                $filePath = FCPATH . $material['file_path'];
                                $fileExists = file_exists($filePath);
                            ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-file-<?= getFileIcon($material['file_name']) ?> text-primary me-2"></i>
                                        <?= esc($material['file_name']) ?>
                                        <?php if (!$fileExists): ?>
                                            <span class="badge bg-danger" title="File not found on server">Missing</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($material['created_at'])) ?></td>
                                    <td>
                                        <?= $fileExists ? formatSizeUnits(filesize($filePath)) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                           class="btn btn-sm btn-primary" 
                                           title="Download"
                                           <?= !$fileExists ? 'disabled' : '' ?>>
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

<style>
    .table th {
        white-space: nowrap;
    }
    .table td {
        vertical-align: middle;
    }
</style>

<script>
// Enable debug panel when pressing Ctrl+Shift+D
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && e.key === 'D') {
        document.querySelector('.alert.alert-info').classList.toggle('d-none');
    }
});
</script>
<?= $this->endSection() ?>