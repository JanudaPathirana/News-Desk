<?php
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT a.*, c.name AS cat, u.name AS author_name
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     JOIN users u ON u.id = a.author_id
                     WHERE a.id = ? AND a.status = 'approved'");
$st->execute([$id]);
$article = $st->fetch();

if (!$article) {
    flash('danger', 'Article not found or pending approval.');
    redirect('news.php');
}

$pageTitle = $article['title'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <span class="badge bg-success mb-2"><?= e($article['cat']) ?></span>
      <h1 class="fw-bold mb-3"><?= e($article['title']) ?></h1>
      <div class="text-muted mb-4 small">
        <span>By <?= e($article['author_name']) ?></span> &bull; 
        <span><?= fmt_date($article['created_at']) ?></span>
      </div>
      <?php if (!empty($article['image'])): ?>
        <img src="<?= img_url($article['image']) ?>" class="img-fluid rounded-4 mb-4 w-100" style="max-height: 440px; object-fit: cover;">
      <?php endif; ?>
      <div class="article-body lh-lg fs-5">
        <?= nl2br(e($article['body'])) ?>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
