<?php
// Set admin page title and active sidebar menu
$adminTitle = 'Dashboard';
$adminActive = 'dash';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

// small function to get one number from the database
function count_rows($pdo, $sql) {
    return (int)$pdo->query($sql)->fetchColumn();
}

$stats = [
    'Published articles' => count_rows($pdo, "SELECT COUNT(*) FROM articles WHERE status = 'approved'"),
    'Pending approval' => count_rows($pdo, "SELECT COUNT(*) FROM articles WHERE status = 'pending'"),
    'Registered students' => count_rows($pdo, "SELECT COUNT(*) FROM users WHERE role = 'student'"),
    'Total views' => count_rows($pdo, "SELECT COALESCE(SUM(views), 0) FROM articles"),
    'Comments' => count_rows($pdo, "SELECT COUNT(*) FROM comments"),
    'Bookmarks' => count_rows($pdo, "SELECT COUNT(*) FROM bookmarks")
];

// articles waiting for approval
$pending = $pdo->query("SELECT a.id, a.title, a.created_at, u.name AS author FROM articles a
                        JOIN users u ON u.id = a.author_id
                        WHERE a.status = 'pending' ORDER BY a.created_at ASC LIMIT 5")->fetchAll();

// most read articles
$top = $pdo->query("SELECT id, title, views FROM articles WHERE status = 'approved' ORDER BY views DESC LIMIT 5")->fetchAll();
$maxViews = max(1, (int)($top[0]['views'] ?? 1));

// latest comments
$recent = $pdo->query("SELECT m.body, m.created_at, u.name, a.title, a.id AS aid FROM comments m
                       JOIN users u ON u.id = m.user_id
                       JOIN articles a ON a.id = m.article_id
                       ORDER BY m.created_at DESC LIMIT 5")->fetchAll();
?>
<div class="row g-3 mb-4">
  <?php foreach ($stats as $label => $number): ?>
    <div class="col-6 col-lg-4 col-xl-2">
      <div class="box text-center">
        <div class="big-number"><?= number_format($number) ?></div>
        <div class="text-muted"><?= $label ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="box h-100">
      <div class="d-flex justify-content-between">
        <h5>Waiting for approval</h5>
        <a href="pending.php">View all</a>
      </div>
      <?php foreach ($pending as $p): ?>
        <div class="d-flex justify-content-between border-bottom py-2">
          <div>
            <a href="../article.php?id=<?= (int)$p['id'] ?>"><?= e($p['title']) ?></a><br>
            <small class="text-muted">by <?= e($p['author']) ?> | <?= fmt_date($p['created_at']) ?></small>
          </div>
          <a class="btn btn-sm btn-outline-dark align-self-center" href="pending.php">Review</a>
        </div>
      <?php endforeach; ?>
      <?php if (!$pending): ?><p class="text-muted mb-0">Nothing is waiting.</p><?php endif; ?>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="box h-100">
      <h5>Most read articles</h5>
      <?php foreach ($top as $t): ?>
        <div class="mb-3">
          <div class="d-flex justify-content-between">
            <span><?= e(excerpt($t['title'], 60)) ?></span>
            <strong><?= (int)$t['views'] ?></strong>
          </div>
          <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-success" style="width: <?= round($t['views'] / $maxViews * 100) ?>%"></div>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (!$top): ?><p class="text-muted mb-0">No published articles yet.</p><?php endif; ?>
    </div>
  </div>

  <div class="col-12">
    <div class="box">
      <div class="d-flex justify-content-between">
        <h5>Latest comments</h5>
        <a href="comments.php">Moderate</a>
      </div>
      <?php foreach ($recent as $c): ?>
        <div class="border-bottom py-2">
          <strong><?= e($c['name']) ?></strong> on <a href="../article.php?id=<?= (int)$c['aid'] ?>"><?= e(excerpt($c['title'], 50)) ?></a><br>
          <small class="text-muted"><?= e(excerpt($c['body'], 120)) ?> | <?= fmt_date($c['created_at']) ?></small>
        </div>
      <?php endforeach; ?>
      <?php if (!$recent): ?><p class="text-muted mb-0">No comments yet.</p><?php endif; ?>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
