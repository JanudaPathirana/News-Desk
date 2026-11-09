<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$old = ['name' => '', 'sid' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $old = ['name' => post('name'), 'sid' => post('student_id'), 'email' => post('email')];
    $pw = $_POST['password'] ?? '';
    $pw2 = $_POST['password2'] ?? '';
    $errors = [];

    // validation
    if (mb_strlen($old['name']) < 2 || mb_strlen($old['name']) > 100) {
        $errors[] = 'Enter your full name.';
    }
    if (!preg_match('/^[A-Za-z0-9]{5,12}$/', $old['sid'])) {
        $errors[] = 'Student ID must be 5 to 12 letters or numbers.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email.';
    }
    if (strlen($pw) < 8) {
        $errors[] = 'Password must have at least 8 characters.';
    }
    if ($pw != $pw2) {
        $errors[] = 'Passwords do not match.';
    }

    // check if email or student id is already used
    if (!$errors) {
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE email = ? OR student_id = ?');
        $chk->execute([$old['email'], $old['sid']]);
        if ($chk->fetchColumn()) {
            $errors[] = 'This email or student ID is already registered.';
        }
    }

    // save the new user (stored as normal plain text)
    if (!$errors) {
        $pdo->prepare("INSERT INTO users (name, student_id, email, password_hash, role) VALUES (?, ?, ?, ?, 'student')")
            ->execute([$old['name'], $old['sid'], $old['email'], $pw]);
        flash('success', 'Account created. You can login now.');
        redirect('login.php');
    }

    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

// Page setup
$pageTitle = 'Register';
$bodyClass = 'auth-page';

// Include header and navigation bar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="box login-box" style="max-width: 500px;">
  <div class="text-center mb-4">
    <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success mb-2">
      <i class="bi bi-person-plus-fill fs-3"></i>
    </div>
    <h3 class="fw-bold mb-1" style="color: var(--nsbm-navy);">Student Registration</h3>
    <p class="text-muted small">Create your account to submit and bookmark news</p>
  </div>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="name">Full Name</label>
      <input class="form-control" id="name" name="name" type="text" placeholder="e.g. Kasun Perera" required value="<?= e($old['name']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="sid">Student ID</label>
      <input class="form-control" id="sid" name="student_id" type="text" placeholder="e.g. ST10001" required pattern="[A-Za-z0-9]{5,12}" value="<?= e($old['sid']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold" for="email">University Email</label>
      <input class="form-control" id="email" name="email" type="email" placeholder="e.g. kasun@students.nsbm.ac.lk" required value="<?= e($old['email']) ?>">
    </div>
    <div class="row g-2 mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="password">Password</label>
        <input class="form-control" id="password" name="password" type="password" required minlength="8" placeholder="Min. 8 chars">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold" for="password2">Confirm</label>
        <input class="form-control" id="password2" name="password2" type="password" required minlength="8" placeholder="Repeat password">
      </div>
    </div>
    <button class="btn btn-green w-100 py-2 shadow-sm">Create Account &raquo;</button>
  </form>
  <div class="pt-3 mt-3 border-top text-center small">
    <p class="mb-0 text-muted">Already registered? <a href="login.php" class="fw-semibold text-success">Login here</a></p>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
