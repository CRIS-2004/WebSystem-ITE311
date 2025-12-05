<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="w-100">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-4">
        <!-- Login form card -->
        <div class="card">
          <div class="card-body p-4">
            <div class="text-center mb-4">
              <h2>Website Sign in</h2>
              <p class="text-muted">Sign In Here!</p>
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

            <!-- Login form -->
            <form method="post" action="<?= base_url('login') ?>">
              <div class="mb-3">
                <label for="login" class="form-label">Email or Username</label>
                <input type="text" class="form-control" id="login" name="login" value="<?= old('login') ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              
              <button type="submit" class="btn btn-primary w-100">Log In</button>
            </form>

            <!-- Link to register page -->
            <div class="text-center mt-3">
              <span>Don't have an account? <a href="<?= base_url('register') ?>" class="btn btn-link p-0">Create Account</a></span><br>
              <span>Back to Main Page? <a href="<?= base_url('/') ?>" class="btn btn-link p-0">Go Back</a></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
    body {
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: #fff;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
      transition: transform 0.3s ease;
      width: 100%;
      max-width: 400px;
      margin: 0 auto;
      padding: 1.5rem;
    }

    .card:hover {
      transform: translateY(-6px);
    }

    h2 {
      color: #00d4ff;
      font-weight: bold;
      text-shadow: 0 0 8px rgba(0, 212, 255, 0.7);
      font-size: 1.75rem;
      margin-bottom: 1rem;
    }
    p{
        font-size: 20px;
        font-weight: bold;
        color: #6faec8ff;
        text-shadow: 0 0 8px rgba(0, 212, 255, 0.7);;
    }

    .form-label {
      color: #e0e0e0;
      font-weight: 500;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      border-radius: 12px;
      padding: 0.65rem 1rem;
      font-size: 1rem;
      margin-bottom: 1rem;
      height: auto;
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
      padding: 0.6rem 1.5rem;
      font-size: 1rem;
      margin: 0.5rem 0;
      width: 100%;
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
      font-size: 1.05rem;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .alert {
      border-radius: 12px;
    }
  </style>
<?= $this->endSection() ?>
