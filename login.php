<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$email = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = post('email');
    $password = $_POST['password'] ?? '';

    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u && ($password === $u['password_hash'] || password_verify($password, $u['password_hash']))) {
        login_user($u);
        flash('success', 'Welcome back, ' . $u['name'] . '!');
        redirect('index.php');
    } else {
        $error = 'Invalid email or password.';
    }
}

$pageTitle = 'Log In';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="box login-box">
    <h3 class="fw-bold mb-3 text-center">Student Login</h3>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input class="form-control" name="email" type="email" required value="<?= e($email) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <button class="btn btn-green w-100" type="submit">Sign In</button>
    </form>
    <div class="text-center mt-3">
      <small>Don't have an account? <a href="register.php">Register</a></small>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
