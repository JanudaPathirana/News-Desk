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

    // save the new user
    if (!$errors) {
        $hash = password_hash($pw, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, student_id, email, password_hash, role) VALUES (?, ?, ?, ?, 'student')")
            ->execute([$old['name'], $old['sid'], $old['email'], $hash]);
        flash('success', 'Account created. You can login now.');
        redirect('login.php');
    }

    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

$pageTitle = 'Register';
$bodyClass = 'auth-page';
require_once __DIR__ . '/includes/header.php';
?>
<div class="box login-box">
  <h2>Create Account</h2>
  <p class="text-muted">For students only.</p>
  <form method="post">
    <?= csrf_field() ?>
    <div class="mb-3">
      <label class="form-label" for="name">Full name</label>
      <input class="form-control" id="name" name="name" type="text" required value="<?= e($old['name']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label" for="sid">Student ID</label>
      <input class="form-control" id="sid" name="student_id" type="text" required pattern="[A-Za-z0-9]{5,12}" value="<?= e($old['sid']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label" for="email">University email</label>
      <input class="form-control" id="email" name="email" type="email" required value="<?= e($old['email']) ?>">
    </div>
    <div class="mb-3">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" required minlength="8">
    </div>
    <div class="mb-3">
      <label class="form-label" for="password2">Confirm password</label>
      <input class="form-control" id="password2" name="password2" type="password" required minlength="8">
    </div>
    <button class="btn btn-green w-100">Register</button>
  </form>
  <p class="mt-3 mb-0 text-center">Already registered? <a href="login.php">Login</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
