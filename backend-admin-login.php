<?php
// separate login page for admins
require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    redirect('index.php');
}

$email = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $email = post('email');
    $password = $_POST['password'] ?? '';

    // only users with admin role can login here
    $st = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u && password_verify($password, $u['password_hash'])) {
        login_user($u);
        flash('success', 'Welcome to the admin panel, ' . $u['name'] . '.');
        redirect('index.php');
    }
    flash('danger', 'Wrong admin email or password.');
}

$base = '../';
$bodyClass = 'auth-page';
$pageTitle = 'Admin Login';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="box login-box">
  <h2>Admin Login</h2>
  <p class="text-muted">Only NewsDesk admins can login here.</p>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label" for="email">Admin email</label>
      <input class="form-control" id="email" name="email" type="email" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" required>
    </div>
    <button class="btn btn-green w-100">Login</button>
  </form>
  <p class="mt-3 mb-0 text-center"><a href="../login.php">Student login</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
