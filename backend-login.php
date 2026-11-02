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

    if ($u && password_verify($password, $u['password_hash'])) {
        login_user($u);
        flash('success', 'Welcome back, ' . $u['name'] . '!');
        redirect($u['role'] == 'admin' ? 'admin/index.php' : 'index.php');
    }
    flash('danger', 'Wrong email or password.');
}

$pageTitle = 'Login';
$bodyClass = 'auth-page';
require_once __DIR__ . '/includes/header.php';
?>
<div class="box login-box">
  <h2>Login</h2>
  <p class="text-muted">Use your NSBM student account.</p>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label" for="email">University email</label>
      <input class="form-control" id="email" name="email" type="email" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" required minlength="6">
    </div>
    <button class="btn btn-green w-100">Login</button>
  </form>
  <p class="mt-3 mb-0 text-center">New here? <a href="register.php">Create an account</a></p>
  <p class="mt-2 mb-0 text-center"><a href="admin/login.php">Admin login</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
