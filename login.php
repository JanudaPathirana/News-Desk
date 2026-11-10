<?php
require_once __DIR__ . '/includes/auth.php';

// already logged in
if (is_logged_in()) {
    redirect(is_admin() ? 'admin/index.php' : 'index.php');
}

$email = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $email = post('email');
    $password = $_POST['password'] ?? '';

    // find the user by email
    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u && ($password === $u['password_hash'] || password_verify($password, $u['password_hash']))) {
        login_user($u);
        flash('success', 'Welcome back, ' . $u['name'] . '!');
        redirect($u['role'] == 'admin' ? 'admin/index.php' : 'index.php');
    }
    flash('danger', 'Wrong email or password.');
}

// Page setup
$pageTitle = 'Login';
$bodyClass = 'auth-page';

// Include header and navigation bar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="box login-box">
  <div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success mb-2">
      <i class="bi bi-person-fill fs-3"></i>
    </div>
    <h3 class="fw-bold mb-1" style="color: var(--nsbm-navy);">Student Login</h3>
    <p class="text-muted small">Sign in with your NSBM university account</p>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="email">University Email</label>
      <input class="form-control" id="email" name="email" type="email" placeholder="e.g. kasun@students.nsbm.ac.lk" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" placeholder="Enter your password" required minlength="6">
    </div>
    <button class="btn btn-green w-100 py-2 mt-2 shadow-sm">Sign In &raquo;</button>
  </form>
  <div class="pt-3 mt-3 border-top text-center small">
    <p class="mb-1 text-muted">Don't have an account? <a href="register.php" class="fw-semibold text-success">Create account</a></p>
    <p class="mb-0"><a href="admin/login.php" class="text-muted"><i class="bi bi-shield-lock me-1"></i>Admin login</a></p>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
