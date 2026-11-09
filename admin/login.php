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

    if ($u && ($password === $u['password_hash'] || password_verify($password, $u['password_hash']))) {
        login_user($u);
        flash('success', 'Welcome to the admin panel, ' . $u['name'] . '.');
        redirect('index.php');
    }
    flash('danger', 'Wrong admin email or password.');
}

$base = '../';
$bodyClass = 'auth-page';
$pageTitle = 'Admin Login';

// Include header and navigation bar
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>
<div class="box login-box">
  <div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-circle bg-primary-subtle text-primary mb-2">
      <i class="bi bi-shield-lock-fill fs-3" style="color: var(--nsbm-navy);"></i>
    </div>
    <h3 class="fw-bold mb-1" style="color: var(--nsbm-navy);">Admin Portal</h3>
    <p class="text-muted small">Only authorized NewsDesk staff & admins</p>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="email">Admin Email</label>
      <input class="form-control" id="email" name="email" type="email" placeholder="admin@nsbm.ac.lk" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" placeholder="Enter admin password" required>
    </div>
    <button class="btn btn-navy w-100 py-2 mt-2 shadow-sm"><i class="bi bi-box-arrow-in-right me-1"></i>Enter Admin Panel</button>
  </form>
  <div class="pt-3 mt-3 border-top text-center small">
    <a href="../login.php" class="text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Student login</a>
  </div>
</div>
<<<<<<< HEAD
<?php require __DIR__ . '/../includes/footer.php'; ?>
=======
<?php require __DIR__ . '/../includes/footer.php'; ?>
>>>>>>> origin/main
