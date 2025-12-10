<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

  <style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
    }

    h2 {
      color: #00d4ff;
      font-weight: bold;
      text-shadow: 0 0 8px rgba(0, 212, 255, 0.7);
    }
    p{
        font-size: 20px;
        font-weight: bold;
        text-shadow: 0 0 8px rgba(0, 212, 255, 0.7);
    }

    .form-label {
      color: #e0e0e0;
      font-weight: 500;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      border-radius: 8px;
      padding: 0.4rem 0.75rem;
      font-size: 0.9rem;
    }

    .form-control:focus {
      border-color: #00d4ff;
      box-shadow: 0 0 8px #00d4ff;
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
    }

    .btn-primary {
      background: linear-gradient(90deg, #00c6ff, #0072ff);
      border: none;
      border-radius: 12px;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 0 12px rgba(0, 212, 255, 0.6);
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #0072ff, #00c6ff);
      transform: scale(1.05);
      box-shadow: 0 0 20px rgba(0, 212, 255, 0.9);
    }
    span{
      color: #ffffffff;
      font-weight: 100px;
    }

    .btn-link {
      color: #00d4ff !important;
      text-decoration: none;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .alert {
      border-radius: 12px;
    }
     select.form-control {
    background-color: rgba(255, 255, 255, 0.1);;
    color:  rgba(255, 255, 255, 1);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  select.form-control:focus {
    background-color: rgba(255, 255, 255, 0.1);;
    color: rgba(0, 0, 0, 1);;
    outline: none;
  }
  </style>
</head>

<body class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <!-- Registration form card -->
        <div class="card">
          <div class="card-body p-2">
            <div class="text-center mb-2">
              <h2 class="h4 mb-1">Join Website</h2>
              <p class="text-muted small">Create your account</p>
            </div>

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

            <?php if (session()->getFlashdata('errors')): ?>
              <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                  <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= $error ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <!-- Registration form -->
            <form method="post" action="<?= base_url('register') ?>">
              <div class="mb-2">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= old('name') ?>" required>
              </div>

              <div class="mb-2">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= old('email') ?>" required>
              </div>

              <div class="mb-2">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="form-text text-light">Minimum 6 characters</div>
              </div>

              <div class="mb-2">
                <label for="password_confirm" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
              </div>
              
              <div class="mb-2">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-control" id="role" name="role" required>
                      <option value="" disabled <?= old('role') ? '' : 'selected' ?>>Select Role</option>
                      <option value="teacher" <?= old('role') == 'teacher' ? 'selected' : '' ?>>Teacher</option>
                      <option value="student" <?= old('role') == 'student' ? 'selected' : '' ?>>Student</option>
                  </select>
              </div>

              <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
                <i class="fas fa-user-plus me-1"></i>Create Account
              </button>
            </form>

            <!-- Link to login page -->
            <div class="text-center mt-2">
              <span>Already have an account? <a href="<?= base_url('login') ?>" class="btn btn-link p-0">Sign In</a></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?= $this->endSection() ?>w