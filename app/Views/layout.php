<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title) ?> | Web System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { padding-top: 100px; 
        background:#2c5364; }
        h1, h2 {
            color: #fff;}
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
                        <a class="nav-link <?= (current_url() == site_url('login')) ? 'active' : '' ?>" href="<?= site_url('login') ?>">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (current_url() == site_url('register')) ? 'active' : '' ?>" href="<?= site_url('register') ?>">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>