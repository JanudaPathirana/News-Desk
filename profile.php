<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$uid = user()['id'];
$st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$st->execute([$uid]);
$currentUser = $st->fetch();

$pageTitle = 'My Profile';
$active = 'profile';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="box p-4 shadow-sm">
        <h3 class="fw-bold mb-4">Student Profile</h3>
        <table class="table">
          <tr><th>Full Name</th><td><?= e($currentUser['name']) ?></td></tr>
          <tr><th>Student ID</th><td><?= e($currentUser['student_id']) ?></td></tr>
          <tr><th>Email Address</th><td><?= e($currentUser['email']) ?></td></tr>
          <tr><th>Role</th><td><span class="badge bg-primary"><?= e(ucfirst($currentUser['role'])) ?></span></td></tr>
          <tr><th>Joined Date</th><td><?= fmt_date($currentUser['created_at']) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
