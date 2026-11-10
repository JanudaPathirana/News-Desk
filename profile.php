<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$uid = user()['id'];
$st = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$st->execute([$uid]);
$currentUser = $st->fetch();

if (!$currentUser) {
    redirect('logout.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $name = post('name');
    $studentId = post('student_id');
    $email = post('email');
    $newPassword = $_POST['password'] ?? '';

    // Validation
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = 'Full name must be between 2 and 100 characters.';
    }

    if ($currentUser['role'] === 'student') {
        if (!preg_match('/^[A-Za-z0-9]{4,15}$/', $studentId)) {
            $errors[] = 'Student ID must be 4 to 15 letters or numbers.';
        }
    } else {
        $studentId = $currentUser['student_id']; // admin student ID kept as is
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($newPassword !== '' && strlen($newPassword) < 6) {
        $errors[] = 'New password must have at least 6 characters.';
    }

    // Check unique email or student ID across other users
    if (!$errors) {
        $chk = $pdo->prepare('SELECT id FROM users WHERE (email = ? OR (student_id = ? AND student_id != "")) AND id != ?');
        $chk->execute([$email, $studentId, $uid]);
        if ($chk->fetch()) {
            $errors[] = 'This email or student ID is already in use by another account.';
        }
    }

    // Profile photo upload
    $newPhoto = null;
    if (!$errors && !empty($_FILES['profile_photo']['name'])) {
        $imgError = '';
        $newPhoto = save_image($_FILES['profile_photo'], $imgError);
        if ($imgError) {
            $errors[] = $imgError;
        }
    }

    if (!$errors) {
        $photoToSave = $newPhoto ? $newPhoto : $currentUser['profile_photo'];

        if ($newPhoto && !empty($currentUser['profile_photo'])) {
            delete_image($currentUser['profile_photo']);
        }

        if ($newPassword !== '') {
            $pdo->prepare('UPDATE users SET name = ?, student_id = ?, email = ?, password_hash = ?, profile_photo = ? WHERE id = ?')
                ->execute([$name, $studentId, $email, $newPassword, $photoToSave, $uid]);
        } else {
            $pdo->prepare('UPDATE users SET name = ?, student_id = ?, email = ?, profile_photo = ? WHERE id = ?')
                ->execute([$name, $studentId, $email, $photoToSave, $uid]);
        }

        refresh_user_session();
        flash('success', 'Your profile details have been successfully updated!');
        redirect('profile.php');
    }

    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

// Refresh current user data for display
$st->execute([$uid]);
$currentUser = $st->fetch();

$pageTitle = 'Edit Profile';
$active = 'profile';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="box shadow-sm">
      <div class="d-flex align-items-center gap-3 pb-3 mb-4 border-bottom">
        <div class="position-relative">
          <img src="<?= user_avatar_url($currentUser, '') ?>" id="avatar-preview" class="rounded-circle border border-3 border-success shadow-sm" width="90" height="90" style="object-fit: cover;" alt="Profile Picture">
        </div>
        <div>
          <h2 class="mb-1"><?= e($currentUser['name']) ?></h2>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success text-capitalize"><?= e($currentUser['role']) ?></span>
            <span class="text-muted small"><?= e($currentUser['email']) ?></span>
            <?php if (!empty($currentUser['student_id'])): ?>
              <span class="text-muted small">&bull; ID: <?= e($currentUser['student_id']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-4">
          <label class="form-label fw-bold" for="profile_photo">Change Profile Photo</label>
          <input class="form-control" type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">
          <div class="form-text">Upload a JPG, PNG, or WebP photo (Max 2MB). A square photo works best.</div>
        </div>

        <div class="row g-3 mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold" for="name">Full Name</label>
            <input class="form-control" id="name" name="name" type="text" required value="<?= e($currentUser['name']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold" for="student_id">Student ID / Admin ID</label>
            <input class="form-control" id="student_id" name="student_id" type="text" required <?= $currentUser['role'] === 'admin' ? 'readonly' : '' ?> value="<?= e($currentUser['student_id']) ?>">
            <?php if ($currentUser['role'] === 'admin'): ?>
              <div class="form-text">Admin ID cannot be changed.</div>
            <?php endif; ?>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold" for="email">Email Address</label>
          <input class="form-control" id="email" name="email" type="email" required value="<?= e($currentUser['email']) ?>">
        </div>

        <div class="mb-4">
          <label class="form-label fw-bold" for="password">Change Password</label>
          <input class="form-control" id="password" name="password" type="text" placeholder="Leave empty to keep current password">
          <div class="form-text">Type a new normal text password if you wish to change it (e.g. <code>123456</code>).</div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
          <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
          <button type="submit" class="btn btn-green px-4 py-2">
            <strong>Save Profile Changes</strong>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatar-preview').src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
