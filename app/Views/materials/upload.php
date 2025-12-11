<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Upload New Material</h4>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?= form_open_multipart(route_to('admin.courses.materials.upload', $course['course_id']), ['id' => 'uploadForm', 'class' => 'mb-4']) ?>
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label for="material">Choose File</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="material" name="material" required>
                                <label class="custom-file-label" for="material">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">
                                Allowed file types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, GIF, MP4 (Max: 100MB)
                            </small>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-1"></i> Upload
                            </button>
                            <a href="<?= route_to('admin.courses.materials.view', $course['course_id']) ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Materials
                            </a>
                        </div>
                    <?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-file-input:focus ~ .custom-file-label {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>

<script>
// Simple file input label update
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('material');
    const fileLabel = document.querySelector('.custom-file-label');
    
    if (fileInput && fileLabel) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileLabel.textContent = this.files[0].name;
            }
        });
    }
});
</script>

<?= $this->endSection() ?>