<?= $this->extend('templates/dashboard_template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= esc($course['course_code']) ?>: <?= esc($course['course_name']) ?></h1>
        <a href="<?= site_url('admin/courses') ?>" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Courses
        </a>
    </div>
    
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    
    <!-- Course Information Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Information</h6>
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

    <!-- Course Materials -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Course Materials</h6>
            <a href="<?= site_url('admin/courses/' . $course['course_id'] . '/materials') ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-list"></i> View All Materials
            </a>
        </div>
        <div class="card-body">
            <!-- Upload Form -->
                    <div class="mb-4">
                        <h6>Upload New Material</h6>
                        <form id="uploadForm" action="<?= site_url('admin/courses/' . $course['course_id'] . '/materials/upload') ?>" method="post" enctype="multipart/form-data" class="mb-4">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="material" name="material" required>
                                </div>
                                <small class="form-text text-muted">
                                    Allowed file types: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, JPG, PNG, GIF, MP4 (Max: 100MB)
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>

                        <!-- Display flash messages if any -->
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                    </div>

            <!-- Recent Materials -->
            <h6>Recent Materials</h6>
            <?php if (!empty($materials)): ?>
                <div class="list-group">
                    <?php foreach (array_slice($materials, 0, 3) as $material): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-alt mr-2"></i>
                                <?= esc($material['file_name']) ?>
                            </div>
                            <div>
                                <a href="<?= site_url('admin/materials/download/' . $material['id']) ?>" 
                                   class="btn btn-sm btn-outline-primary mr-1"
                                   data-toggle="tooltip" 
                                   title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <?php if (session()->get('isLoggedIn')): ?>
                                    <form action="<?= site_url('admin/courses/' . $course['course_id'] . '/materials/delete/' . $material['id']) ?>" 
                                            method="post" 
                                            class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this material?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No materials have been uploaded yet.</p>
            <?php endif; ?>
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
<?php $this->section('scripts') ?>
<!-- Page level plugins -->
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('material');
    const fileLabel = document.getElementById('fileLabel');
    const uploadButton = document.getElementById('uploadButton');
    const uploadButtonText = document.getElementById('uploadButtonText');
    const uploadSpinner = document.getElementById('uploadSpinner');
    const uploadStatus = document.getElementById('uploadStatus');
    // Update file label when a file is selected
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            fileLabel.textContent = this.files[0].name;
        }
    });
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadUrl = this.action;
            
            // Show loading state
            uploadButton.disabled = true;
            uploadButtonText.innerHTML = 'Uploading...';
            if (uploadSpinner) uploadSpinner.classList.remove('d-none');
            if (uploadStatus) uploadStatus.innerHTML = '';
            // Send AJAX request
            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    if (uploadStatus) {
                        uploadStatus.innerHTML = `<div class="alert alert-success py-1 small">${data.message}</div>`;
                    }
                    // Refresh the page after 1.5 seconds to show the new material
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (uploadStatus) {
                    uploadStatus.innerHTML = `<div class="alert alert-danger py-1 small">${error.message || 'An error occurred during upload'}</div>`;
                }
            })
            .finally(() => {
                // Reset button state
                if (uploadButton) uploadButton.disabled = false;
                if (uploadButtonText) uploadButtonText.innerHTML = '<i class="fas fa-upload"></i> Upload';
                if (uploadSpinner) uploadSpinner.classList.add('d-none');
            });
        });
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap custom file input
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
    // Handle file upload form
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        const uploadButton = uploadForm.querySelector('button[type="submit"]');
        const uploadButtonText = uploadForm.querySelector('#uploadButtonText');
        const uploadSpinner = uploadForm.querySelector('#uploadSpinner');
        
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const uploadUrl = this.action;
            
            // Show loading state
            uploadButton.disabled = true;
            uploadButtonText.innerHTML = 'Uploading...';
            if (uploadSpinner) uploadSpinner.classList.remove('d-none');
            
            // Clear any previous messages
            const previousAlerts = document.querySelectorAll('.alert');
            previousAlerts.forEach(alert => alert.remove());
            
            // Send AJAX request
            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Create alert element
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show mt-3`;
                alertDiv.role = 'alert';
                
                alertDiv.innerHTML = `
                    ${data.message || (data.success ? 'File uploaded successfully' : 'Upload failed')}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                
                // Insert alert before the form
                uploadForm.parentNode.insertBefore(alertDiv, uploadForm);
                
                // If successful, reload the page after a short delay
                if (data.success) {
                    setTimeout(() => window.location.reload(), 1500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    ${error.message || 'An error occurred during upload'}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                `;
                uploadForm.parentNode.insertBefore(alertDiv, uploadForm);
            })
            .finally(() => {
                // Reset button state
                if (uploadButton) uploadButton.disabled = false;
                if (uploadButtonText) uploadButtonText.innerHTML = '<i class="fas fa-upload"></i> Upload';
                if (uploadSpinner) uploadSpinner.classList.add('d-none');
            });
        });
    }
    // Handle delete material form submission
    document.querySelectorAll('.delete-material-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
                const form = this;
                const button = form.querySelector('button[type="submit"]');
                const originalHtml = button.innerHTML;
                const row = form.closest('.list-group-item');
                
                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                
                // Get CSRF token from the form
                const csrfToken = form.querySelector('[name="csrf_test_name"]').value;
                
                // Create form data
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('csrf_test_name', csrfToken);
                
                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    // Create alert element
                    const alertDiv = document.createElement('div');
                    alertDiv.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show mt-3`;
                    alertDiv.role = 'alert';
                    
                    alertDiv.innerHTML = `
                        ${data.message || (data.success ? 'File uploaded successfully' : 'Upload failed')}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `;
                    
                    // Insert alert before the form
                    uploadForm.parentNode.insertBefore(alertDiv, uploadForm);
                    
                    // If successful, reload the page after a short delay
                    if (data.success) {
                        setTimeout(() => {
                            // Clear any existing alerts before reloading
                            const alerts = document.querySelectorAll('.alert');
                            alerts.forEach(alert => alert.remove());
                            window.location.reload();
                        }, 1000);
                    }
        });
    };
    // Initialize tooltips
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Initialize DataTables for better table display if available
    if (typeof $.fn.DataTable === 'function') {
        $('table').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            order: [[0, 'asc']]
        });
    }
});

    // Initialize Bootstrap custom file input
    if (typeof bsCustomFileInput !== 'undefined') {
        bsCustomFileInput.init();
    }
    
    // Handle file input display
    const fileInput = document.querySelector('.custom-file-input');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Choose file';
            const label = e.target.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    }

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize DataTables for better table display
    if ($.fn.DataTable) {
        $('table').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            order: [[0, 'asc']]
        });
    }
});
// Handle delete material
// Replace the existing delete material JavaScript with this:
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete material form submission
    document.querySelectorAll('.delete-material-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
                const form = this;
                const button = form.querySelector('button[type="submit"]');
                const originalHtml = button.innerHTML;
                const row = form.closest('.list-group-item');
                
                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                
                // Get CSRF token from the form
                const csrfToken = form.querySelector('[name="csrf_test_name"]').value;
                
                // Create form data
                const formData = new FormData();
                formData.append('_method', 'DELETE');
                formData.append('csrf_test_name', csrfToken);
                
                // Send AJAX request
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text || 'Network response was not ok');
                        });
                    }
                    return response.json();
                })
                .then(data => {
    // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show mt-3`;
        alertDiv.role = 'alert';
        
        alertDiv.innerHTML = `
            ${data.message || (data.success ? 'File uploaded successfully' : 'Upload failed')}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
        
        // Insert alert before the form
        uploadForm.parentNode.insertBefore(alertDiv, uploadForm);
        
        // If successful, reload the page after a short delay
        if (data.success) {
            setTimeout(() => {
                // Clear any existing alerts before reloading
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
                window.location.reload();
            }, 1000);
    }
})
            }
        });
    });
});
fetch(this.action, {
    method: 'POST',  // Important: Use POST here
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-CSRF-TOKEN': csrfToken
    },
    body: formData.toString()
})
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadForm');
    const uploadButton = document.getElementById('uploadButton');
    const uploadButtonText = document.getElementById('uploadButtonText');
    const uploadSpinner = document.getElementById('uploadSpinner');
    const uploadStatus = document.getElementById('uploadStatus');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const xhr = new XMLHttpRequest();
        
        // Show loading state
        uploadButton.disabled = true;
        uploadButtonText.textContent = 'Uploading...';
        uploadSpinner.classList.remove('d-none');
        uploadStatus.textContent = '';
        
        xhr.open('POST', this.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        
        xhr.onload = function() {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    // Show success message
                    const successAlert = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message || 'File uploaded successfully'}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    
                    // Insert alert before the form
                    form.insertAdjacentHTML('beforebegin', successAlert);
                    
                    // Reload the page after a short delay to show the success message
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    // Show error message
                    const errorAlert = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message || 'Error processing upload'}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `;
                    
                    // Insert alert before the form
                    form.insertAdjacentHTML('beforebegin', errorAlert);
                }
            } catch (error) {
                // Show error message
                const errorAlert = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${error.message || 'Error processing upload'}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `;
                
                // Insert alert before the form
                form.insertAdjacentHTML('beforebegin', errorAlert);
            } finally {
                // Reset form and button
                uploadButton.disabled = false;
                uploadButtonText.textContent = 'Upload';
                uploadSpinner.classList.add('d-none');
                form.reset();
            }
        };
        
        xhr.onerror = function() {
            uploadStatus.className = 'text-danger';
            uploadStatus.textContent = 'Network error occurred';
            uploadButton.disabled = false;
            uploadButtonText.textContent = 'Upload';
            uploadSpinner.classList.add('d-none');
        };
        
        xhr.send(formData);
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>