<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Course Materials - <?= esc($course['course_name']) ?></h1>
        <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload New Material</h6>
        </div>
        <div class="card-body">
           <?= form_open('teacher/courses/materials/upload/' . $course['course_id'], ['enctype' => 'multipart/form-data', 'class' => 'mb-4', 'id' => 'uploadForm']) ?>
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="material">Choose File</label>
        <div class="custom-file">
            <input type="file" class="custom-file-input" id="material" name="material" required>
        </div>
        <small class="form-text text-muted">
            Allowed file types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, GIF, MP4 (Max: 100MB)
        </small>
    </div>
    <button type="submit" class="btn btn-primary" id="uploadBtn">
        <i class="fas fa-upload"></i> Upload
    </button>
    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger mt-3" id="errorAlert">
            <?= session('error') ?>
            <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
        </div>
    <?php endif; ?>
<?= form_close() ?>
<!-- Display uploaded materials -->
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Uploaded Materials</h6>
        </div>
        <div class="card-body">
            <?php if (empty($materials)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-file-upload fa-4x text-gray-300 mb-3"></i>
                    <p class="mb-0">No materials have been uploaded yet.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Uploaded On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td>
                                        <i class="fas fa-file"></i>
                                        <?= esc($material['file_name']) ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($material['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url($material['file_path']) ?>" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank"
                                           download>
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $material['id'] ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this material? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="post" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(materialId) {
    const form = document.getElementById('deleteForm');
    form.action = "<?= site_url('teacher/courses/materials/delete/') ?>" + materialId;
    $('#deleteModal').modal('show');
}

// Update file input label
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.querySelector('.custom-file-input');
    const fileLabel = document.querySelector('.custom-file-label');
    
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileLabel.textContent = this.files[0].name;
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Update file input label
    const fileInput = document.getElementById('material');
    const fileLabel = fileInput.nextElementSibling;
    
    fileInput.addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'Choose file...';
        fileLabel.textContent = fileName;
    });
    // Handle form submission
    const form = document.getElementById('uploadForm');
    const uploadBtn = document.getElementById('uploadBtn');
    
    form.addEventListener('submit', function(e) {
        // Show loading state
        uploadBtn.disabled = true;
        uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
    });
    // Handle material deletion
    document.querySelectorAll('.delete-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.dataset.id;
            const courseId = this.dataset.courseId;
            
            if (confirm('Are you sure you want to delete this material?')) {
                fetch(`/teacher/courses/${courseId}/materials/delete/${materialId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="csrf_test_name"]').value
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the material from the list
                        this.closest('.list-group-item').remove();
                        // Show success message
                        showAlert('Material deleted successfully', 'success');
                    } else {
                        throw new Error(data.message || 'Failed to delete material');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert(error.message, 'danger');
                });
            }
        });
    });
    // Helper function to show alerts
    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show mt-3`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        // Insert after the form
        form.parentNode.insertBefore(alertDiv, form.nextSibling);
        
        // Auto-remove alert after 5 seconds
        setTimeout(() => {
            alertDiv.style.opacity = '0';
            setTimeout(() => alertDiv.remove(), 300);
        }, 5000);
    }
    // Auto-hide success/error messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
