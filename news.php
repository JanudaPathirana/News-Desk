<?php
$pageTitle = 'All News';
$active = 'news';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/card.php';

$st = $pdo->query("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                   FROM articles a JOIN categories c ON c.id = a.category_id
                   WHERE a.status = 'approved' ORDER BY a.created_at DESC");
$rows = $st->fetchAll();
?>
<main class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Campus News Archive</h2>
    <span class="text-muted"><?= count($rows) ?> articles published</span>
  </div>
  <div class="row g-4">
    <?php foreach ($rows as $a): news_card($a); endforeach; ?>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
