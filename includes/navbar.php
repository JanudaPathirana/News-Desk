<?php
// Admin top navbar and sidebar navigation
$adminTitle = $adminTitle ?? 'Dashboard';
$adminActive = $adminActive ?? '';

// Number of articles pending approval for badge
$pendingCount = 0;
try {
    $pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'")->fetchColumn();
} catch (Exception $e) {
    // If articles table not created yet in early development
    $pendingCount = 0;
}

// Admin sidebar links (Using absolute paths to prevent any relative folder mixups)
$links = [
    'dash' => ['/News-Desk/admin/index.php', 'Dashboard'],
    'articles' => ['/News-Desk/admin/articles.php', 'Articles'],
    'pending' => ['/News-Desk/admin/pending.php', 'Pending Approvals'],
    'featured' => ['/News-Desk/admin/featured.php', 'Featured / Breaking'],
    'cats' => ['/News-Desk/admin/categories.php', 'Categories'],
    'ann' => ['/News-Desk/admin/announcements.php', 'Announcements'],
    'comments' => ['/News-Desk/admin/comments.php', 'Comments'],
    'reports' => ['/News-Desk/admin/reports.php', 'Reports']
];
?>
<!-- Admin Top Navbar -->
<nav class="navbar navbar-dark navbar-green no-print">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/News-Desk/admin/index.php">NSBM <span>NewsDesk</span> Admin</a>
    <div class="d-flex align-items-center gap-2">
      <a class="btn btn-sm btn-outline-light" href="/News-Desk/index.php" target="_blank">
        <i class="bi bi-box-arrow-up-right me-1"></i>View Site
      </a>
      <a href="/News-Desk/profile.php" class="d-flex align-items-center text-white text-decoration-none px-2 py-1 rounded">
        <img src="<?= user_avatar_url(null, '/News-Desk/') ?>" alt="<?= e(user()['name']) ?>" width="32" height="32" class="rounded-circle border border-2 border-white me-2 shadow-sm" style="object-fit: cover;">
        <span class="fw-semibold text-truncate" style="max-width: 140px;"><?= e(user()['name']) ?></span>
      </a>
      <a class="btn btn-sm btn-light-green" href="/News-Desk/logout.php">Logout</a>
    </div>
  </div>
</nav>

<!-- Admin Layout Container -->
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar Navigation -->
    <div class="col-md-2 sidebar py-3 no-print">
      <?php foreach ($links as $key => $link): ?>
        <a class="<?= $adminActive == $key ? 'active' : '' ?>" href="<?= $link[0] ?>">
          <?= $link[1] ?>
          <?php if ($key == 'pending' && $pendingCount > 0): ?>
            <span class="badge bg-warning text-dark"><?= $pendingCount ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Main Admin Content Area -->
    <div class="col-md-10 py-3">
      <h2 class="mb-3"><?= e($adminTitle) ?></h2>
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
