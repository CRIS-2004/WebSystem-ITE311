<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<?php 
// Debug output
log_message('debug', 'Rendering student/course/view.php');
log_message('debug', 'Materials data: ' . print_r(isset($materials) ? $materials : 'No materials variable', true));
log_message('debug', 'Course data: ' . print_r(isset($course) ? $course : 'No course variable', true));
?>
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-3">
        <a href="<?= site_url('student/dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
    
    <!-- Course Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        </div>
        <div class="d-flex align-items-center">
            <!-- View All Materials Button -->
           <a href="<?= site_url('student/course/' . ($course['course_id'] ?? 0) . '/materials') ?>" 
   class="btn btn-primary btn-sm me-2" id="viewAllMaterialsBtn">
    <i class="fas fa-book me-1"></i> View All Materials
    <?php 
    // Get the count from available variables
    $count = 0;
    if (isset($materialsCount)) {
        $count = $materialsCount;
    } elseif (isset($materials_count)) {
        $count = $materials_count;
    } elseif (isset($materials) && is_array($materials)) {
        $count = count($materials);
    }
    
    if ($count > 0): ?>
        (<?= $count ?>)
    <?php endif; ?>
</a>
            <?php if (isset($isEnrolled) && $isEnrolled): ?>
                <div class="text-center">
                    <div class="progress-circle d-inline-block" data-value="<?= $progress ?? 0 ?>">
                        <svg class="progress-circle-svg" viewBox="0 0 100 100">
                            <circle class="progress-circle-bg" cx="50" cy="50" r="45" />
                            <circle class="progress-circle-fill" cx="50" cy="50" r="45" 
                                    style="--progress: <?= ($progress ?? 0) / 100 ?>;" />
                        </svg>
                        <div class="progress-circle-text"><?= $progress ?? 0 ?>%</div>
                    </div>
                    <div class="text-muted mt-2">Course Progress</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    <!-- Course Content Tabs -->
    <div class="row">
        <div class="col-lg-12">
    <!-- Course Overview -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <!-- Course Description -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">About This Course</h6>
                </div>
                <div class="card-body">
                    <?= $course['description'] ?? 'No description available for this course.' ?>
                </div>
            </div>

            <!-- Course Materials Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Materials</h6>
            <span class="badge bg-primary"><?= count($materials ?? []) ?> Materials</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($materials)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($materials as $material): 
                        $filePath = FCPATH . 'uploads/materials/' . $material['file_path'];
                        $fileExists = file_exists($filePath);
                        $fileExt = pathinfo($material['file_path'], PATHINFO_EXTENSION);
                        $iconClass = function_exists('getFileIconClass') ? getFileIconClass($fileExt) : 'fa-file';
                    ?>
                        <div class="list-group-item border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas <?= $iconClass ?> fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0"><?= esc($material['file_name']) ?></h6>
                                        <small class="text-muted">
                                            <?= date('M d, Y', strtotime($material['created_at'])) ?> • 
                                            <?= $fileExists ? formatSizeUnits(filesize($filePath)) : 'File not found' ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Download"
                                       <?= !$fileExists ? 'disabled' : '' ?>>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No materials have been uploaded for this course yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Course Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Course Details</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-user-graduate me-2 text-primary"></i>
                            <strong>Instructor:</strong> <?= esc($course['instructor_name'] ?? ($course['course_instructor'] ?? 'Not specified')) ?>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-door-open me-2 text-primary"></i>
                            <strong>Room:</strong> <?= esc($course['room'] ?? ($course['room'] ?? 'Not specified')) ?>
                        </li>
                        <li class="mb-2">
                            <i class="far fa-calendar-alt me-2 text-primary"></i>
                            <strong>Last Updated:</strong> <?= date('M d, Y', strtotime($course['updated_at'] ?? 'now')) ?>
                        </li>
                        <?php if (isset($modules) && is_array($modules)): ?>
                        <li class="mb-2">
                            <i class="fas fa-tasks me-2 text-primary"></i>
                            <strong>Modules:</strong> <?= count($modules) ?>
                        </li>
                        <?php endif; ?>
                        <?php if ($isEnrolled): ?>
                            <li class="mb-2">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>
                                <strong>Enrolled On:</strong> <?= date('M d, Y', strtotime($enrollment['enrollment_date'] ?? 'now')) ?>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-chart-line me-2 text-primary"></i>
                                <strong>Progress:</strong> <?= $progress ?>% Complete
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- What You'll Learn -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">What You'll Learn</h6>
                </div>
                <div class="card-body">
                    <ul class="list-checked">
                        <li>Understand key concepts and principles</li>
                        <li>Apply knowledge to real-world scenarios</li>
                        <li>Develop practical skills</li>
                        <li>Complete hands-on projects</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Alert -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9998; width: 350px;">
    <div id="enrollmentAlert" class="alert alert-dismissible fade" role="alert">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .progress-circle {
        position: relative;
        width: 60px;
        height: 60px;
    }
    
    .progress-circle-svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    
    .progress-circle-bg {
        fill: none;
        stroke: #e9ecef;
        stroke-width: 8;
    }
    
    .progress-circle-fill {
        fill: none;
        stroke: #4e73df;
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 283;
        stroke-dashoffset: calc(283 - (283 * var(--progress)));
        transition: stroke-dashoffset 0.5s ease;
    }
    
    .progress-circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.8rem;
        font-weight: bold;
        color: #4e73df;
    }
    
    .list-checked {
        list-style: none;
        padding-left: 0;
    }
    
    .list-checked li {
        position: relative;
        padding-left: 1.8rem;
        margin-bottom: 0.5rem;
    }
    
    .list-checked li:before {
        content: '✓';
        position: absolute;
        left: 0;
        color: #28a745;
    }
    
    .lesson-item {
        border-radius: 4px;
        transition: all 0.2s;
    }
    
    .lesson-item:hover {
        background-color: #f8f9fa;
    }
    
    .lesson-item.completed {
        color: #6c757d;
    }
    
    .lesson-item.completed .fa-play-circle {
        color: #28a745;
    }
    .table th {
    white-space: nowrap;
    font-size: 0.85rem;
}
.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}
.progress-circle {
    position: relative;
    width: 80px;
    height: 80px;
}
.progress-circle-svg {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}
.progress-circle-bg {
    fill: none;
    stroke: #f3f3f3;
    stroke-width: 8;
}
.progress-circle-fill {
    fill: none;
    stroke: #4e73df;
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 283;
    stroke-dashoffset: calc(283 * (1 - var(--progress, 0)));
    transition: stroke-dashoffset 0.5s ease;
}
.progress-circle-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1rem;
    font-weight: bold;
    color: #4e73df;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Handle enroll button click
    $(document).on('click', '.enroll-btn', function() {
        const button = $(this);
        const courseId = button.data('course-id');
        
        // Update button state
        button.html('<span class="spinner-border spinner-border-sm me-1" role="status"></span>Enrolling...').prop('disabled', true);
        
        // Send AJAX request
        $.ajax({
            url: '<?= site_url('course/enroll') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
                'course_id': courseId
            },
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('success', response.message);
                    // Reload the page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message || 'An error occurred. Please try again.');
                    button.html('<i class="fas fa-plus-circle me-1"></i> Enroll Now').prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Enrollment error:', status, error);
                showAlert('danger', 'An error occurred while processing your request. Please try again.');
                button.html('<i class="fas fa-plus-circle me-1"></i> Enroll Now').prop('disabled', false);
            }
        });
    });
    
    // Function to show alerts
    function showAlert(type, message) {
        const alert = $('#enrollmentAlert');
        alert.removeClass('alert-success alert-danger alert-warning')
             .addClass(`alert-${type} show`)
             .find('#alertMessage')
             .html(`<i class="${type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'} me-2"></i>${message}`);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.removeClass('show');
        }, 5000);
    }
    
    // Handle lesson item clicks
    $('.lesson-item').on('click', function(e) {
        e.preventDefault();
        // Add your lesson viewing logic here
        console.log('Lesson clicked');
    });
});
</script>
<?= $this->endSection() ?>