<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/card.php';

$breaking = $pdo->query("SELECT id, title FROM articles WHERE status = 'approved' AND is_breaking = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();
$featured = $pdo->query("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                         FROM articles a JOIN categories c ON c.id = a.category_id
                         WHERE a.status = 'approved' AND a.is_featured = 1 ORDER BY a.created_at DESC LIMIT 2")->fetchAll();
$latest = $pdo->query("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                       FROM articles a JOIN categories c ON c.id = a.category_id
                       WHERE a.status = 'approved' ORDER BY a.created_at DESC LIMIT 6")->fetchAll();

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-4">
  <?php if (!empty($breaking)): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4 py-2 px-3 shadow-sm">
      <span class="badge bg-danger">BREAKING</span>
      <div class="text-truncate">
        <?php foreach ($breaking as $b): ?>
          <a href="article.php?id=<?= (int)$b['id'] ?>" class="text-danger fw-semibold text-decoration-none me-3">&bull; <?= e($b['title']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="campus-banner text-center mb-5">
    <h1 class="display-5 fw-bold text-white mb-2">NSBM NewsDesk</h1>
    <p class="text-white-50 lead">The official campus news and media hub of NSBM Green University</p>
  </div>

  <?php if (!empty($featured)): ?>
    <div class="mb-5">
      <h3 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i>Featured Stories</h3>
      <div class="row g-4">
        <?php foreach ($featured as $a): news_card($a); endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-12"><h3 class="fw-bold mb-1">Latest Campus News</h3></div>
    <?php foreach ($latest as $a): news_card($a); endforeach; ?>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
