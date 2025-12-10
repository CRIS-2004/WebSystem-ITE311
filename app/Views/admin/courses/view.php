<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($course['course_code']) ?>: <?= esc($course['course_name']) ?></h1>
        <a href="<?= site_url('admin/courses') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Courses
        </a>
    </div>

    <!-- Course Details Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
            <a href="<?= site_url('admin/courses/edit/' . $course['course_id']) ?>" 
               class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i> Edit Course
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Course Code:</strong> <?= esc($course['course_code']) ?></p>
                    <p><strong>Course Name:</strong> <?= esc($course['course_name']) ?></p>
                    <p><strong>Instructor:</strong> 
                        <?= esc($course['instructor_name']) ?> 
                        (<?= esc($course['instructor_email']) ?>)
                    </p>
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
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Description</h5>
                    <p><?= nl2br(esc($course['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrolled Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Enrolled Students</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($enrolledStudents)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Enrollment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?php foreach ($enrolledStudents as $student): ?>
                                    <tr>
                                        <td><?= $student['user_id'] ?></td>
                                        <td><?= esc($student['student_name']) ?></td>
                                        <td><?= esc($student['student_email']) ?></td>
                                        <td><?= !empty($student['enrolled_at']) ? date('M d, Y', strtotime($student['enrolled_at'])) : 'N/A' ?></td>
                                        <td>
                                            <form action="<?= site_url('admin/courses/remove-student/' . $course['course_id'] . '/' . $student['user_id']) ?>" 
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

    <!-- Add Students Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Add Students to Course</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($availableStudents)): ?>
                <form action="<?= site_url('admin/courses/add-student/' . $course['course_id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="user_id">Select Student</label>
                        <select class="form-control" id="user_id" name="user_id" required>
                            <option value="">-- Select Student --</option>
                            <?php foreach ($availableStudents as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= esc($student['name']) ?> (<?= esc($student['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add Student
                    </button>
                </form>
            <?php else: ?>
                <div class="alert alert-info">All students are already enrolled in this course.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>