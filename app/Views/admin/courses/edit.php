<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Course</h1>
        <a href="<?= site_url('admin/courses') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Courses
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Course Details</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $error): ?>
                                <?= $error ?><br>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('admin/courses/update/' . $course['course_id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="course_code">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" 
                                   value="<?= old('course_code', $course['course_code']) ?>" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="course_name">Course Name</label>
                            <input type="text" class="form-control" id="course_name" name="course_name" 
                                   value="<?= old('course_name', $course['course_name']) ?>" required>
                        </div>

                        <div class="form-group mt-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                     rows="4" required><?= old('description', $course['description']) ?></textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label for="course_instructor">Instructor</label>
                            <select class="form-control" id="course_instructor" name="course_instructor" required>
                                <option value="">Select Instructor</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher['id'] ?>" 
                                        <?= old('course_instructor', $course['course_instructor']) == $teacher['id'] ? 'selected' : '' ?>>
                                        <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cn_number">Course Number</label>
                                    <input type="text" class="form-control" id="cn_number" name="cn_number" 
                                           value="<?= old('cn_number', $course['cn_number'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="room">Room</label>
                                    <input type="text" class="form-control" id="room" name="room" 
                                           value="<?= old('room', $course['room'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_date">Schedule Date</label>
                                    <input type="date" class="form-control" id="schedule_date" name="schedule_date" 
                                           value="<?= old('schedule_date', $course['schedule_date'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="schedule_time">Schedule Time</label>
                                    <input type="time" class="form-control" id="schedule_time" name="schedule_time" 
                                           value="<?= old('schedule_time', $course['schedule_time'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Update Course</button>
                            <a href="<?= site_url('admin/courses') ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>