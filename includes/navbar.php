<?php
// Navigation bar component
$base = $base ?? '';
$active = $active ?? '';

// Menu links definition
$menu = [
    'home' => ['index.php', 'Home'],
    'news' => ['news.php', 'All News'],
    'submit' => ['submit.php', 'Submit Article'],
    'bookmarks' => ['bookmarks.php', 'Saved'],
    'subs' => ['subscriptions.php', 'Subscriptions']
];
?>
<!-- Main Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-green no-print">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= $base ?>index.php">NSBM <span>NewsDesk</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menu">
      <!-- Navigation Links -->
      <ul class="navbar-nav me-auto">
        <?php foreach ($menu as $key => $item): ?>
          <li class="nav-item">
            <a class="nav-link <?= $active == $key ? 'active' : '' ?>" href="<?= $base . $item[0] ?>"><?= $item[1] ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <!-- User / Auth Buttons -->
      <div class="d-flex align-items-center gap-2">
        <?php if (is_logged_in()): ?>
          <?php if (is_admin()): ?>
            <a class="btn btn-sm btn-light-green" href="<?= $base ?>admin/index.php">
              <i class="bi bi-speedometer2 me-1"></i>Admin Panel
            </a>
          <?php endif; ?>

          <!-- User Profile Dropdown with Photo -->
          <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle px-2 py-1 rounded hover-bg" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?= user_avatar_url(null, $base) ?>" alt="<?= e(user()['name']) ?>" width="34" height="34" class="rounded-circle border border-2 border-white me-2 shadow-sm" style="object-fit: cover;">
              <span class="fw-semibold text-truncate" style="max-width: 140px;"><?= e(user()['name']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenuDropdown">
              <li class="px-3 py-2 text-muted border-bottom small">
                Signed in as <strong class="text-capitalize text-success"><?= e(user()['role']) ?></strong>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 <?= $active == 'profile' ? 'active' : '' ?>" href="<?= $base ?>profile.php">
                  <i class="bi bi-person-circle text-primary"></i> Edit Profile
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $base ?>my-submissions.php">
                  <i class="bi bi-journal-text text-success"></i> My Submissions
                </a>
              </li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="<?= $base ?>bookmarks.php">
                  <i class="bi bi-bookmark-star text-warning"></i> Saved Articles
                </a>
              </li>
              <li><hr class="dropdown-divider my-1"></li>
              <li>
                <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="<?= $base ?>logout.php">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </a>
              </li>
            </ul>
          </div>
        <?php else: ?>
          <a class="btn btn-sm btn-outline-light" href="<?= $base ?>login.php">Login</a>
          <a class="btn btn-sm btn-light-green" href="<?= $base ?>register.php">Register</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<!-- Main Page Content Container -->
<main class="container py-4 flex-grow-1">
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
  <div class="alert alert-<?= $type ?> alert-dismissible fade show shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
    <i class="bi <?= $icon ?> fs-5"></i>
    <div class="flex-grow-1"><?= $f[1] ?></div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endforeach; ?>
