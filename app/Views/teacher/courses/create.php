<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create New Course</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <?php foreach (session('errors') as $error): ?>
                        <p class="mb-0"><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('teacher/courses/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="course_code" class="required">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" 
                                   value="<?= old('course_code') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="cn_number">CN Number</label>
                            <input type="text" class="form-control" id="cn_number" name="cn_number" 
                                   value="<?= old('cn_number') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="course_name" class="required">Course Name</label>
                    <input type="text" class="form-control" id="course_name" name="course_name" 
                           value="<?= old('course_name') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="schedule_date">Schedule Date</label>
                            <input type="date" class="form-control" id="schedule_date" name="schedule_date" 
                                   value="<?= old('schedule_date') ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="schedule_time">Schedule Time</label>
                            <input type="time" class="form-control" id="schedule_time" name="schedule_time" 
                                   value="<?= old('schedule_time') ?>">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="room">Room</label>
                    <input type="text" class="form-control" id="room" name="room" 
                           value="<?= old('room') ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="5"><?= old('description') ?></textarea>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    label {
        font-weight: 600;
    }
    .required:after {
        content: " *";
        color: #e74a3b;
    }
</style>

<?= $this->endSection() ?>