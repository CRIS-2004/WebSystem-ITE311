<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Teachers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalTeachers ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Students</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalStudents ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
            <a href="<?= base_url('admin/users/new') ?>" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php 
                        // Get all users
                        $userModel = new \App\Models\UserModel();
                        $users = $userModel->orderBy('created_at', 'DESC')->findAll(5); // Show latest 5 users
                        
                        foreach ($users as $user): 
                        ?>
                        <tr>
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'Admin' ? 'bg-primary' : 
                                                   ($user['role'] === 'Teacher' ? 'bg-success' : 'bg-info') ?>">
                                    <?= $user['role'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['role'] !== 'Admin'): ?>
                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" 
                                   class="btn btn-sm btn-primary" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($user['id'] != session()->get('userID')): ?>
                                <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user?')" 
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
             <div class="text-center mt-3">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-primary">View All Users</a>
            </div><br>
            <!-- Courses Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Recent Courses</h6>
        <a href="<?= site_url('admin/courses') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-book"></i> View All Courses
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Instructor</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentCourses)): ?>
                        <?php foreach ($recentCourses as $course): ?>
                        <tr>
                            <td><?= esc($course['course_code']) ?></td>
                            <td><?= esc($course['course_name']) ?></td>
                            <td><?= esc($course['instructor_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                            <td>
                                <a href="<?= site_url('admin/courses/view/' . $course['course_id']) ?>" 
                                   class="btn btn-info btn-sm" 
                                   title="View Course">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No courses found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Page level plugins -->
<script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js') ?>"></script>

<!-- Page level custom scripts -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[2, "desc"]] // Sort by role by default
        });
    });
</script>
<?= $this->endSection() ?>