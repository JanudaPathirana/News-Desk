<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';
$name = '';
$student_id = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = post('name');
    $student_id = strtoupper(post('student_id'));
    $email = post('email');
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($student_id) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        $st = $pdo->prepare('SELECT id FROM users WHERE email = ? OR student_id = ?');
        $st->execute([$email, $student_id]);
        if ($st->fetch()) {
            $error = 'Email or Student ID is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare('INSERT INTO users (name, student_id, email, password_hash, role) VALUES (?, ?, ?, ?, "student")');
            $ins->execute([$name, $student_id, $email, $hash]);
            flash('success', 'Registration successful! You can now log in.');
            redirect('login.php');
        }
    }
}

$pageTitle = 'Student Registration';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="box login-box">
    <h3 class="fw-bold mb-3 text-center">Student Registration</h3>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input class="form-control" name="name" required value="<?= e($name) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Student ID (e.g. ST10001)</label>
        <input class="form-control" name="student_id" required value="<?= e($student_id) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email" required value="<?= e($email) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input class="form-control" name="password" type="password" required>
      </div>
      <button class="btn btn-green w-100" type="submit">Register</button>
    </form>
    <div class="text-center mt-3">
      <small>Already registered? <a href="login.php">Log In</a></small>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
