<?= $this->extend('templates/dashboard_template') ?>

<?php
// Ensure $user is always an array with all required keys
if (!isset($user) || !is_array($user)) {
    $user = [
        'name' => '',
        'email' => '',
        'role' => ''
    ];
}
?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= isset($user) ? 'Edit' : 'Add New' ?> User</h1>
        <a href="<?= base_url('admin/users') ?>" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Users
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
        </div>
        <div class="card-body">
        <?php if (!empty($user['id'])): ?>
            <form action="<?= base_url('admin/users/update/' . ($user['id'] ?? '')) ?>" method="post">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                <input type="hidden" name="_method" value="PUT">
        <?php else: ?>
            <form action="<?= base_url('admin/users/store') ?>" method="post">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
        <?php endif; ?>

                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control <?= session('errors.name') ? 'is-invalid' : '' ?>" 
                            id="name" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required>
                        <?php if (session('errors.name')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.name') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                               id="email" name="email" value="<?= old('email', $user['email'] ?? '') ?>" required>
                        <?php if (session('errors.email')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.email') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="role" class="col-sm-2 col-form-label">Role <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                       <select class="form-control <?= session('errors.role') ? 'is-invalid' : '' ?>" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="Admin" <?= (old('role', $user['role'] ?? '') == 'Admin') ? 'selected' : '' ?>>Admin</option>
                            <option value="Teacher" <?= (old('role', $user['role'] ?? '') == 'Teacher') ? 'selected' : '' ?>>Teacher</option>
                            <option value="Student" <?= (old('role', $user['role'] ?? '') == 'Student') ? 'selected' : '' ?>>Student</option>
                        </select>
                        <?php if (session('errors.role')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.role') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session('errors.role')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.role') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                                <div class="form-group row mt-3">
                    <label for="password" class="col-sm-2 col-form-label">Password <?= !isset($user) ? '<span class="text-danger">*</span>' : '' ?></label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                            id="password" name="password" <?= !isset($user) ? 'required' : '' ?>>
                        <?php if (session('errors.password')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.password') ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($user)): ?>
                            <small class="text-muted">Leave blank to keep current password</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row mt-3">
                    <label for="password_confirm" class="col-sm-2 col-form-label">
                        <?= isset($user) ? 'Confirm New Password' : 'Confirm Password' ?>
                        <?= !isset($user) ? '<span class="text-danger">*</span>' : '' ?>
                    </label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" 
                               id="password_confirm" name="password_confirm" <?= !isset($user) ? 'required' : '' ?>>
                        <?php if (session('errors.password_confirm')): ?>
                            <div class="invalid-feedback">
                                <?= session('errors.password_confirm') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group row mt-4">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?= isset($user) ? 'Update' : 'Save' ?> User
                        </button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Client-side validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length > 0 && password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long!');
                return false;
            }
        });
    });
</script>
<?= $this->endSection() ?>
