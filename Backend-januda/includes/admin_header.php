<?php
// layout for all admin pages (sidebar on the left)
// before including set: $adminTitle, $adminActive
require_once __DIR__ . '/auth.php';
require_admin();
$base = '../';
$adminTitle = $adminTitle ?? 'Dashboard';
$adminActive = $adminActive ?? '';

// number of articles waiting for approval (shown in the sidebar)
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM articles WHERE status = 'pending'")->fetchColumn();

$links = [
    'dash' => ['index.php', 'Dashboard'],
    'articles' => ['articles.php', 'Articles'],
    'pending' => ['pending.php', 'Pending Approvals'],
    'featured' => ['featured.php', 'Featured / Breaking'],
    'cats' => ['categories.php', 'Categories'],
    'ann' => ['announcements.php', 'Announcements'],
    'comments' => ['comments.php', 'Comments'],
    'reports' => ['reports.php', 'Reports']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($adminTitle) ?> | NewsDesk Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark navbar-green no-print">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">NSBM <span>NewsDesk</span> Admin</a>
    <div>
      <a class="btn btn-sm btn-outline-light" href="../index.php" target="_blank">View Site</a>
      <span class="text-white mx-2"><?= e(user()['name']) ?></span>
      <a class="btn btn-sm btn-light-green" href="../logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
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

    <div class="col-md-10 py-3">
      <h2 class="mb-3"><?= e($adminTitle) ?></h2>
      <?php foreach (get_flash() as $f): ?>
        <div class="alert alert-<?= e($f[0]) ?>"><?= e($f[1]) ?></div>
      <?php endforeach; ?>
