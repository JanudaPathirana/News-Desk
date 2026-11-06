<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/card.php';
require_login();

$uid = user()['id'];
$st = $pdo->prepare("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                     FROM bookmarks b
                     JOIN articles a ON a.id = b.article_id
                     JOIN categories c ON c.id = a.category_id
                     WHERE b.user_id = ? AND a.status = 'approved'
                     ORDER BY b.created_at DESC");
$st->execute([$uid]);
$rows = $st->fetchAll();

$pageTitle = 'Saved Bookmarks';
$active = 'bookmarks';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <h2 class="fw-bold mb-4">Saved Articles</h2>
  <div class="row g-4">
    <?php if (empty($rows)): ?>
      <div class="col-12"><p class="text-muted">No saved articles yet.</p></div>
    <?php else: ?>
      <?php foreach ($rows as $a): news_card($a); endforeach; ?>
    <?php endif; ?>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
