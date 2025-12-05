<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Welcome Card -->
        <div class="col-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Welcome, <?= esc($user['name']) ?>!</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= ucfirst(esc($user['role'])) ?> Dashboard
                            </div>
                            <div class="mt-2">
                                <p class="mb-1"><strong>Email:</strong> <?= esc($user['email']) ?></p>
                                <p class="mb-0"><strong>Role:</strong> <?= ucfirst(esc($user['role'])) ?></p>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-specific content -->
        <?php if ($user['role'] === 'admin'): ?>
            <!-- Admin Dashboard Content -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Admin Controls</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('admin/users') ?>" class="btn btn-primary btn-block">
                                    <i class="fas fa-users fa-fw"></i> Manage Users
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('admin/settings') ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-cog fa-fw"></i> System Settings
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('admin/reports') ?>" class="btn btn-info btn-block">
                                    <i class="fas fa-chart-bar fa-fw"></i> View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($user['role'] === 'teacher'): ?>
            <!-- Teacher Dashboard Content -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Teaching Tools</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('teacher/classes') ?>" class="btn btn-primary btn-block">
                                    <i class="fas fa-chalkboard-teacher fa-fw"></i> My Classes
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('teacher/assignments') ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-tasks fa-fw"></i> Assignments
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('teacher/grades') ?>" class="btn btn-info btn-block">
                                    <i class="fas fa-graduation-cap fa-fw"></i> Gradebook
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Student Dashboard Content -->
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">My Learning</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('student/courses') ?>" class="btn btn-primary btn-block">
                                    <i class="fas fa-book fa-fw"></i> My Courses
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('student/schedule') ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-calendar-alt fa-fw"></i> Class Schedule
                                </a>
                            </div>
                            <div class="col-md-4 mb-4">
                                <a href="<?= base_url('student/grades') ?>" class="btn btn-info btn-block">
                                    <i class="fas fa-chart-line fa-fw"></i> My Grades
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>