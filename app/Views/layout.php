<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?> | Web System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/css/notifications.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <style>
        body { padding-top: 100px; 
        background:#2c5364; }
        h1, h2 {
            color: #fff;}
        
        /* Notification dropdown styles */
        .notification-dropdown {
            width: 350px;
            max-height: 500px;
            overflow-y: auto;
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
        
        .notification-badge {
            font-size: 0.7rem;
            padding: 0.25em 0.5em;
            top: 5px !important;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url('/') ?>">Web System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == site_url('/') || current_url() == site_url('index')) ? 'active' : '' ?>" href="<?= site_url('/') ?>">Home</a>
                    </li>
                    <li class = "nav-item">
                        <a class="nav-link <?= (current_url() == site_url('about')) ? 'active' : '' ?>" href="<?=site_url('about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == site_url('contact')) ? 'active' : '' ?>" href="<?= site_url('contact') ?>">Contact</a>
                    </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <?php if (session()->has('isLoggedIn')): ?>
                        <li class="nav-item dropdown notification-container">
                            <a class="nav-link notification-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge" style="display: none;">
                                    0
                                </span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end notification-dropdown p-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border-bottom mb-2">
                                    <h6 class="mb-0">Notifications</h6>
                                </div>
                                <div class="notifications-container">
                                    <div class="text-center p-3 text-muted">Loading notifications...</div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= site_url('logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                        <?php else: ?>
                        <a class="nav-link <?= (current_url() == site_url('login')) ? 'active' : '' ?>" href="<?= site_url('login') ?>">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <li class="nav-item">
                            <a class="nav-link <?= (current_url() == site_url('register')) ? 'active' : '' ?>" href="<?= site_url('register') ?>">
                                <i class="fas fa-user-plus"></i> Register
                            </a>
                        </li>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>
    <?php 
    echo "<!-- Debug: base_url: " . base_url('js/notifications.js') . " -->\n";
    echo "<!-- Debug: document_root: " . $_SERVER['DOCUMENT_ROOT'] . " -->\n";
?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url('public/js/notifications.js') ?>"></script>
</body>
</html>