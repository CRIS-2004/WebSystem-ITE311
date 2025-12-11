<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($course['course_code']) ?>: <?= esc($course['course_name']) ?></h1>
        <a href="<?= site_url('teacher/dashboard') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
        </a>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Course Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
            <div>
                <a href="<?= site_url('teacher/courses/edit/' . $course['course_id']) ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit Course
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course Code:</strong> <?= esc($course['course_code']) ?></p>
                    <p><strong>Course Name:</strong> <?= esc($course['course_name']) ?></p>
                    <p><strong>Description:</strong> <?= nl2br(esc($course['description'])) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Room:</strong> <?= esc($course['room'] ?? 'Not specified') ?></p>
                    <p><strong>Schedule:</strong> 
                        <?= !empty($course['schedule_date']) ? date('l, F j, Y', strtotime($course['schedule_date'])) : 'Not scheduled' ?>
                        <?php if (!empty($course['schedule_time'])): ?>
                            at <?= date('h:i A', strtotime($course['schedule_time'])) ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Materials</h6>
            <a href="<?= site_url('teacher/courses/' . $course['course_id'] . '/materials') ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-list"></i> View All Materials
            </a>
        </div>
        <div class="card-body">
            <!-- Upload Form -->
            <div class="mb-4">
                <h6>Upload New Material</h6>
                <form action="<?= site_url('teacher/courses/materials/upload/' . $course['course_id']) ?>" 
                        method="post" 
                        enctype="multipart/form-data"
                        id="uploadForm">
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Choose file</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" id="uploadButton">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </form>
            </div>

            <!-- Recent Materials -->
            <h6>Recent Materials</h6>
            <?php if (!empty($materials)): ?>
                <div class="list-group">
                    <?php foreach (array_slice($materials, 0, 3) as $material): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt mr-2"></i>
                                <?= esc($material['file_name']) ?>
                            </div>
                            <div>
                                <a href="<?= site_url('materials/download/' . $material['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary mr-1"
                                   data-toggle="tooltip" 
                                   title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php if (session()->get('isLoggedIn')): ?>
                                    <form action="<?= site_url('teacher/materials/delete/' . $material['id']) ?>" 
                                          method="post" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this material?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                data-toggle="tooltip" 
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No materials have been uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enrolled Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Enrolled Students</h6>
            <a href="<?= site_url('teacher/courses/manage-students/' . $course['course_id']) ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-user-plus"></i> Manage Students
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($enrolledStudents)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="enrolledStudentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Date Enrolled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolledStudents as $student): ?>
                                <tr>
                                    <td><?= esc($student['student_id'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['name']) ?></td>
                                    <td><?= esc($student['email']) ?></td>
                                    <td><?= date('M d, Y', strtotime($student['enrolled_at'])) ?></td>
                                    <td>
                                        <form action="<?= site_url('teacher/courses/remove-student/' . $course['course_id'] . '/' . $student['id']) ?>" 
                                              method="post" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to remove this student from the course?');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-user-minus"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No students are currently enrolled in this course.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<!-- Page level plugins -->
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<script>
    // Initialize file input
    $(document).ready(function() {
        bsCustomFileInput.init();
        
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
        
        // Initialize DataTable for enrolled students
        if ($.fn.DataTable.isDataTable('#enrolledStudentsTable')) {
            $('#enrolledStudentsTable').DataTable().destroy();
        }
        
        $('#enrolledStudentsTable').DataTable({
            "pageLength": 10,
            "order": [[3, 'desc']]
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadButton = document.getElementById('uploadButton');
            const originalButtonText = uploadButton.innerHTML;
            
            // Show loading state
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('File uploaded successfully');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                console.error('Error:', error);
            })
            .finally(() => {
                uploadButton.disabled = false;
                uploadButton.innerHTML = originalButtonText;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>

<!-- Remove Student Confirmation Modal -->
<div class="modal fade" id="removeStudentModal" tabindex="-1" role="dialog" aria-labelledby="removeStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeStudentModalLabel">Confirm Removal</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this student from the course?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger" id="confirmRemove" href="#">Remove</a>
            </div>
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 600;
    }
    .action-buttons .btn {
        margin-right: 5px;
    }
</style>

<?= $this->section('scripts') ?>
<script>
    console.log("Form action URL:", '<?= site_url('teacher/courses/materials/upload/' . $course['course_id']) ?>');
    document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadButton = document.getElementById('uploadButton');
            const originalButtonText = uploadButton.innerHTML;
            
            // Show loading state
            uploadButton.disabled = true;
            uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
            
            // Make sure the URL is correct
            const url = '<?= site_url('teacher/courses/materials/upload/' . $course['course_id']) ?>';
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('File uploaded successfully');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
                console.error('Error:', error);
            })
            .finally(() => {
                uploadButton.disabled = false;
                uploadButton.innerHTML = originalButtonText;
            });
        });
    }
});
    $(document).ready(function() {
        // Initialize DataTable
        $('#enrolledStudentsTable').DataTable({
            "pageLength": 10,
            "order": [[1, "asc"]]
        });

        // Handle remove student button click
        $('.remove-student').click(function() {
            const studentId = $(this).data('student-id');
            const courseId = $(this).data('course-id');
            const removeUrl = '<?= site_url('teacher/courses/remove-student/') ?>' + courseId + '/' + studentId;
            
            $('#confirmRemove').attr('href', removeUrl);
            $('#removeStudentModal').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>

