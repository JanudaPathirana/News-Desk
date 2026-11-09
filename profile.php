<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$uid = user()['id'];
$st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$st->execute([$uid]);
$currentUser = $st->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = post('name');
    $password = $_POST['password'] ?? '';

    if ($name) {
        $pdo->prepare('UPDATE users SET name = ? WHERE id = ?')->execute([$name, $uid]);
        $_SESSION['user']['name'] = $name;
    }
    if ($password) {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $uid]);
            flash('success', 'Password updated successfully.');
            redirect('profile.php');
        }
    }
}

$pageTitle = 'My Profile';
$active = 'profile';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="box p-4 shadow-sm">
        <h3 class="fw-bold mb-3">Profile & Security</h3>
        <?php foreach ($errors as $err): ?>
          <div class="alert alert-danger"><?= e($err) ?></div>
        <?php endforeach; ?>
        <form method="post">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input class="form-control" name="name" value="<?= e($currentUser['name']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input class="form-control" name="password" type="password">
          </div>
          <button class="btn btn-green" type="submit">Update Profile</button>
        </form>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
