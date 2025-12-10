<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<!-- Loading Overlay -->
<div id="loadingOverlay" class="position-fixed w-100 h-100 bg-dark bg-opacity-50 d-none" style="top:0; left:0; z-index:9999;">
    <div class="position-absolute top-50 start-50 translate-middle">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<!-- Alert Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9998; width: 350px;">
    <div id="enrollmentAlert" class="alert alert-dismissible fade" role="alert">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Student Dashboard</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>
    
    <!-- Enrolled Courses Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
            <h6 class="m-0 font-weight-bold">My Enrolled Courses</h6>
            <span class="badge bg-light text-primary"><?= count($enrolledCourses) ?> Enrolled</span>
        </div>
        <div class="card-body">
            <?php if (!empty($enrolledCourses)): ?>
                <div class="row">
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-left-primary shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="card-title text-primary"><?= esc($course['course_name']) ?></h5>
                                            <span class="badge bg-success text-white">Enrolled</span>
                                        </div>
                                        <p class="card-text text-muted small"><?= esc($course['description'] ?? 'No description available') ?></p>
                                        <div class="mt-auto">
                                            <div class="progress mb-2" style="height: 5px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <p class="small text-muted mb-2">Enrolled on: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <a href="<?= site_url('course/view/' . $course['course_id']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-book-open me-1"></i> View Course
                                        </a>
                                        <span class="text-muted small">0% Complete</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">You haven't enrolled in any courses yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Available Courses Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Available Courses</h6>
            <span class="badge bg-light text-primary"><?= count($availableCourses) ?> Available</span>
        </div>
        <div class="card-body">
            <?php if (!empty($availableCourses)): ?>
                <div class="row">
                    <?php foreach ($availableCourses as $course): 
                        $isEnrolled = false;
                        foreach ($enrolledCourses as $enrolled) {
                            if ($enrolled['course_id'] === $course['course_id']) {
                                $isEnrolled = true;
                                break;
                            }
                        }
                    ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 border-left-info shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-3">
                                        <h5 class="card-title text-primary"><?= esc($course['course_name']) ?></h5>
                                        <p class="card-text text-muted small"><?= esc($course['description'] ?? 'No description available') ?></p>
                                        <div class="d-flex align-items-center mt-3 mb-2">
                                            <div class="me-3">
                                                <i class="fas fa-user-tie text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 small text-muted">Instructor</p>
                                                <p class="mb-0 fw-bold"><?= esc($course['instructor_name'] ?? ($course['course_instructor'] ?? 'Not specified')) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <?php if ($isEnrolled): ?>
                                            <button class="btn btn-success btn-block w-100" disabled>
                                                <i class="fas fa-check-circle me-1"></i> Already Enrolled
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-primary btn-block w-100 enroll-btn" data-course-id="<?= $course['course_id'] ?>">
                                                <i class="fas fa-plus-circle me-1"></i> Enroll Now
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No courses available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Show loading overlay during AJAX requests
    $(document).ajaxStart(function() {
        $('#loadingOverlay').removeClass('d-none');
    }).ajaxStop(function() {
        $('#loadingOverlay').addClass('d-none');
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

    // Handle enroll button click
    $(document).on('click', '.enroll-btn', function() {
        const button = $(this);
        const courseId = button.data('course-id');
        const card = button.closest('.card');
        
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
                    
                    // Animate card removal
                    card.fadeOut(300, function() {
                        // Check if no more courses available
                        if ($('.enroll-btn').length <= 1) { // <=1 because we're in the middle of removing one
                            $('.card-body > .row').html(`
                                <div class="col-12 text-center py-5">
                                    <div class="text-muted mb-3">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h4>No more courses available to enroll in.</h4>
                                        <p class="mb-0">You've enrolled in all available courses.</p>
                                    </div>
                                    <a href="<?= site_url('dashboard') ?>" class="btn btn-primary mt-3">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                                    </a>
                                </div>
                            `);
                        }
                        
                        // Reload the page after a short delay to show updated enrolled courses
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    });
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
    
    // Close button for alert
    $('.alert .btn-close').on('click', function() {
        $(this).closest('.alert').removeClass('show').addClass('fade');
    });
});
</script>
<?= $this->endSection() ?>