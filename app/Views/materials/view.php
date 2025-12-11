<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book mr-2"></i><?= esc($course['course_name']) ?> - Materials
                    </h4>
                    <div class="d-flex">
                        <a href="<?= site_url('admin/courses/view/' . $course['course_id']) ?>" 
                           class="btn btn-light btn-sm border">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Course
                        </a>
                        <?php if (session()->get('role') === 'admin'): ?>
                            <a href="<?= site_url('admin/courses/' . $course['course_id'] . '/materials/upload') ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-upload mr-1"></i> Upload Material
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($materials)): ?>
                        <div class="alert alert-info">
                            No materials have been uploaded for this course yet.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?= esc($material['file_name']) ?></h6>
                                        <small class="text-muted">
                                            Uploaded on: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('admin/materials/download/' . $material['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <?php if (session()->get('role') === 'admin'): ?>
                                            <a href="<?= site_url('admin/materials/delete/' . $material['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Are you sure you want to delete this material?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>