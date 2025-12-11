<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Teacher Dashboard</h1>
        <a href="<?= site_url('teacher/courses/create') ?>" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">Add New Course</span>
        </a>
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">My Courses</h6>
        </div>
        <div class="card-body">
            <?php if (empty($courses)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-book-open fa-4x text-gray-300 mb-3"></i>
                    <p class="mb-0">You haven't created any courses yet.</p>
                    <a href="<?= site_url('teacher/courses/create') ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Create Your First Course
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>CN Number</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Schedule Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= esc($course['cn_number'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                    <td><?= esc($course['course_name']) ?><br>
                                        <small class="text-muted"><?= esc($course['description']) ?></small>
                                    <td><?= date('M d, Y', strtotime($course['created_at'])) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($course['schedule_date'])): ?>
                                            <?= date('D, M j', strtotime($course['schedule_date'])) ?>
                                            <?php if (!empty($course['schedule_time'])): ?>
                                                <br><small class="text-muted"><?= date('h:i A', strtotime($course['schedule_time'])) ?></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Not scheduled
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($course['room'] ?? 'TBA') ?></td>
                                    <td>
                                        <a href="<?= site_url('teacher/courses/students/' . $course['course_id']) ?>" 
                                            class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-users"></i> View Students
                                            </a>
                                        <a href="<?= site_url('teacher/courses/' . $course['course_id'] . '/materials') ?>" 
                                           class="btn btn-sm btn-success mb-1" 
                                           title="View Course">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?= site_url('teacher/courses/edit/' . $course['course_id']) ?>" 
                                           class="btn btn-sm btn-primary mb-1" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= site_url('teacher/courses/delete/' . $course['course_id']) ?>" 
                                              method="post" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
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
<?= $this->endSection() ?>