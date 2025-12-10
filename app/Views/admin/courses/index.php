<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">All Courses</h1>
            <a href="<?= site_url('admin/courses/create') ?>" class="btn btn-success btn-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Course
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="coursesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Instructor</th>
                            <th>Room</th>
                            <th>Schedule</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= esc($course['course_code']) ?></td>
                            <td><?= esc($course['course_name']) ?></td>
                            <td><?= esc($course['instructor_name']) ?></td>
                            <td><?= esc($course['room'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($course['schedule_date'])): ?>
                                    <?= date('M d, Y', strtotime($course['schedule_date'])) ?>
                                    <?= !empty($course['schedule_time']) ? 'at ' . $course['schedule_time'] : '' ?>
                                <?php else: ?>
                                    Not scheduled
                                <?php endif; ?>
                            </td>
                            <td><?= date('M d, Y', strtotime($course['created_at'])) ?></td>
                            <td>
                                <a href="<?= site_url('admin/courses/view/' . $course['course_id']) ?>" 
                                   class="btn btn-info btn-sm" 
                                   title="View Course">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/courses/edit/' . $course['course_id']) ?>" 
                                        class="btn btn-warning btn-sm" 
                                        title="Edit Course">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                <form action="<?= site_url('admin/courses/delete/' . $course['course_id']) ?>" 
                                      method="post" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Course">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-secondary btn-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Dashboard
            </a>
</div>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('#coursesTable').DataTable({
            "order": [[5, "desc"]] // Sort by created_at by default
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>