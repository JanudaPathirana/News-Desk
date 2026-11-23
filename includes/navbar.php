<?php
// Front-end (student) navbar
$active = $active ?? '';
$base = $base ?? '';
?>

<!-- Student / Public Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-green no-print">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">NSBM <span>NewsDesk</span></a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link <?= $active == 'home' ? 'active' : '' ?>" href="<?= $base ?>index.php"><i class="bi bi-house me-1"></i>Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $active == 'news' ? 'active' : '' ?>" href="<?= $base ?>news.php"><i class="bi bi-newspaper me-1"></i>All News</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link <?= $active == 'submit' ? 'active' : '' ?>" href="<?= $base ?>submit.php"><i class="bi bi-plus-circle me-1"></i>Submit Article</a></li>
          <li class="nav-item"><a class="nav-link <?= $active == 'bookmarks' ? 'active' : '' ?>" href="<?= $base ?>bookmarks.php"><i class="bi bi-bookmark me-1"></i>Saved</a></li>
          <li class="nav-item"><a class="nav-link <?= $active == 'subs' ? 'active' : '' ?>" href="<?= $base ?>subscriptions.php"><i class="bi bi-bell me-1"></i>Subscriptions</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?>
            <a class="btn btn-sm btn-outline-light" href="<?= $base ?>admin/index.php"><i class="bi bi-speedometer2 me-1"></i>Admin Panel</a>
          <?php endif; ?>
          <a href="<?= $base ?>profile.php" class="d-flex align-items-center text-white text-decoration-none px-2 py-1 rounded hover-bg">
            <img src="<?= user_avatar_url(null, $base) ?>" alt="<?= e(user()['name']) ?>" width="30" height="30" class="rounded-circle border border-2 border-white me-2 shadow-sm" style="object-fit: cover;">
            <span class="fw-semibold d-none d-md-inline text-truncate" style="max-width: 120px;"><?= e(user()['name']) ?></span>
          </a>
          <a class="btn btn-sm btn-light-green" href="<?= $base ?>logout.php">Logout</a>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-light" href="<?= $base ?>login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
          <a class="btn btn-sm btn-light-green" href="<?= $base ?>register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<main class="container my-4">
  <!-- Flash messages -->
  <?php foreach (get_flash() as $f): ?>
    <?php
      $type = e($f[0]);
      $icon = match($type) {
        'success' => 'bi-check-circle-fill',
        'danger'  => 'bi-exclamation-triangle-fill',
        'warning' => 'bi-exclamation-circle-fill',
        default   => 'bi-info-circle-fill'
      };
    ?>
    <div class="alert alert-<?= $type ?> alert-dismissible fade show shadow-sm d-flex align-items-center gap-2 mb-3" role="alert">
      <i class="bi <?= $icon ?> fs-5"></i>
      <div class="flex-grow-1"><?= $f[1] ?></div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endforeach; ?>
