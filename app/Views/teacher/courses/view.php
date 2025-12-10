<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($course['course_name']) ?> - Enrolled Students</h1>
        <div>
            <a href="<?= site_url('teacher/courses/edit/' . $course['course_id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Course
            </a>
            <a href="<?= site_url('teacher/courses/manage-students/' . $course['course_id']) ?>" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Manage Students
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
            <h6 class="m-0 font-weight-bold">Enrolled Students</h6>
            <span class="badge bg-light text-primary"><?= count($enrolledStudents) ?> Students</span>
        </div>
        <div class="card-body">
            <?php if (empty($enrolledStudents)): ?>
                <div class="alert alert-info">No students are currently enrolled in this course.</div>
            <?php else: ?>
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
                                        <button class="btn btn-sm btn-danger remove-student" 
                                                data-student-id="<?= $student['id'] ?>"
                                                data-course-id="<?= $course['course_id'] ?>">
                                            <i class="fas fa-user-minus"></i> Remove
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

