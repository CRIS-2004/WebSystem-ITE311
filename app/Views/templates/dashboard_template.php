<!-- app/Views/templates/dashboard_template.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'LMS Dashboard' ?></title>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .status-badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

/* Add some spacing between action buttons */
.btn-sm {
    margin-right: 0.25rem;
}
        /* Notification dropdown styles */
        .notification-dropdown {
            width: 320px;
            max-height: 500px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,.15);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            border-radius: 0.35rem;
        }
        
        .notification-badge {
            font-size: 0.6rem;
            padding: 0.25em 0.5em;
            top: 5px !important;
            right: -5px !important;
            left: auto !important;
            transform: none !important;
        }
        
        .notification-container {
            position: relative;
        }
        
        .notification-toggle {
            position: relative;
            padding: 0.5rem;
            color: #6c757d;
        }
        
        .notification-toggle:hover {
            color: #0d6efd;
        }
        
        .notification-item {
            border-left: 3px solid transparent;
            transition: all 0.2s;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.25rem;
        }
        
        .notification-item.unread {
            border-left-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }
        
        .notification-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .sidebar {
            width: 150px; /* Set fixed width */
            min-height: 100vh;
            background: #4e73df;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            transition: all 0.3s ease;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        .sidebar .nav-link:hover {
            color: #f7f3f3ff;
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .topbar {
            height: 4.375rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
    </style>
</head>
<body>
    
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="text-center py-4">
                <h4 class="text-white">Web System</h4>
            </div>
            <hr class="bg-light">
            <ul class="nav flex-column">
                <!-- Navigation items removed as requested -->
                <!-- Add more sidebar links as needed -->
            </ul>
        </div>

        <!-- Content Wrapper -->
        <div class="flex-grow-1">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                <div class="container-fluid">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    <!-- Left-aligned items -->
                    <div class="d-flex align-items-center">
                        <!-- Add any left-aligned items here if needed -->
                    </div>
                    
                    <!-- Right-aligned items -->
                    <div class="d-flex align-items-center ms-auto">
                        <!-- Notification Dropdown -->
                        <div class="nav-item dropdown no-arrow mx-2 notification-container">
                            <a class="nav-link notification-toggle" href="#" role="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none; font-size: 0.6rem; padding: 0.25em 0.5em;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-2" aria-labelledby="notificationDropdown" style="width: 320px;">
                                <div class="d-flex justify-content-between align-items-center p-2 border-bottom mb-2">
                                    <h6 class="mb-0">Notifications</h6>
                                    <small><a href="#" class="text-primary">Mark all as read</a></small>
                                </div>
                                <div class="notifications-container" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center p-3 text-muted">Loading notifications...</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small"><?= session()->get('name') ?? 'User' ?></span>
                                <i class="fas fa-user-circle fa-fw"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
            <!-- End of Page Content -->
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Notification Script -->
    <script src="/js/notifications.js"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggleTop').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('d-none');
        });

        // CSRF token setup for AJAX
        var csrfName = '<?= csrf_token() ?>';
        var csrfHash = '<?= csrf_hash() ?>';

        // Global AJAX setup
        $.ajaxSetup({
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            }
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>