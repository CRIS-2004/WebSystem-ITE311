<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary btn-sm mb-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="h3 mb-0 text-gray-800"><?= esc($course['course_name']) ?> - Manage Students</h1>
        </div>
    </div>

    <?php if (session()->has('message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('message') ?>
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

    <div class="row">
        <!-- Available Students -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Available Students</h6>
                    <span class="badge bg-light text-primary"><?= count($availableStudents) ?> Students</span>
                </div>
                <div class="card-body">
                    <?php if (empty($availableStudents)): ?>
                        <div class="alert alert-info">All students are enrolled in this course.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($availableStudents as $student): ?>
                                        <tr>
                                            <td><?= esc($student['name']) ?></td>
                                            <td><?= esc($student['email']) ?></td>
                                            <td>
                                                <form action="<?= site_url('teacher/courses/students/add/' . $course['course_id'] . '/' . $student['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-user-plus"></i> Add
                                                    </button>
                                                </form>
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

        <!-- Enrolled Students -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Enrolled Students</h6>
                    <span class="badge bg-light text-primary"><?= count($enrolledStudents) ?> Students</span>
                </div>
                <div class="card-body">
                    <?php if (empty($enrolledStudents)): ?>
                        <div class="alert alert-info">No students are currently enrolled in this course.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Enrolled</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($enrolledStudents as $student): ?>
                                        <tr>
                                            <td><?= esc($student['name']) ?></td>
                                            <td><?= esc($student['email']) ?></td>
                                            <td><?= date('M d, Y', strtotime($student['enrolled_at'])) ?></td>
                                            <td>
                                                <form action="<?= site_url('teacher/courses/students/remove/' . $course['course_id'] . '/' . $student['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this student from the course?');">
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
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
