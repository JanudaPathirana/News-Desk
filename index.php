<?php
// Set page title and active navigation item
$pageTitle = 'Home';
$active = 'home';

// Include page header, navbar, and card components
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/card.php';

// main news at the top (breaking first, then featured, then newest)
$hero = $pdo->query("SELECT a.*, c.name AS cat FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     WHERE a.status = 'approved'
                     ORDER BY a.is_breaking DESC, a.is_featured DESC, a.created_at DESC LIMIT 1")->fetch();

// latest 3 announcements
$anns = $pdo->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3")->fetchAll();

// latest news (subscribed categories come first)
$uid = user()['id'] ?? 0;
$st = $pdo->prepare("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat,
                            (s.user_id IS NOT NULL) AS subscribed
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     LEFT JOIN subscriptions s ON s.category_id = a.category_id AND s.user_id = ?
                     WHERE a.status = 'approved' AND a.id <> ?
                     ORDER BY subscribed DESC, a.created_at DESC LIMIT 6");
$st->execute([$uid, $hero['id'] ?? 0]);
$latest = $st->fetchAll();

// categories with article count
$cats = $pdo->query("SELECT c.id, c.name, COUNT(a.id) AS total FROM categories c
                     LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'approved'
                     GROUP BY c.id, c.name ORDER BY c.name")->fetchAll();
?>

<section class="campus-banner">
  <p class="banner-small">NSBM Green University</p>
  <h1>What's happening on campus?</h1>
  <p>News, events and notices from your faculty and clubs, all in one place.</p>
  <form class="d-flex gap-2 mb-3" action="news.php" method="get" style="max-width:480px">
    <input class="form-control rounded-pill bg-white text-dark shadow-sm border-0 px-3" type="text" name="q" placeholder="Search campus news, events...">
    <button class="btn btn-green px-4 shadow-sm"><i class="bi bi-search me-1"></i>Search</button>
  </form>
  <a class="btn btn-outline-light" href="submit.php"><i class="bi bi-plus-circle me-1"></i>Submit an article</a>
</section>

<?php if ($hero): ?>
<div class="box border-0 shadow-sm p-4">
  <div class="row g-4 align-items-center">
    <div class="col-md-5">
      <img class="hero-img" src="<?= img_url($hero['image'], $base) ?>" alt="main news">
    </div>
    <div class="col-md-7">
      <div class="d-flex gap-1 mb-2 flex-wrap">
        <?php if ($hero['is_breaking']): ?><span class="badge bg-danger text-white shadow-sm"><i class="bi bi-lightning-fill me-1"></i>Breaking</span><?php endif; ?>
        <?php if ($hero['is_featured']): ?><span class="badge bg-primary text-white shadow-sm"><i class="bi bi-star-fill me-1"></i>Featured</span><?php endif; ?>
        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold"><?= e($hero['cat']) ?></span>
      </div>
      <h2 class="mt-2 fw-bold" style="color: var(--nsbm-navy);"><?= e($hero['title']) ?></h2>
      <p class="text-muted fs-6"><?= e(excerpt($hero['body'], 180)) ?></p>
      <p class="text-muted small mb-3"><i class="bi bi-calendar3 me-1 text-primary"></i><?= fmt_date($hero['created_at']) ?> &bull; <i class="bi bi-eye me-1 text-success"></i><?= (int)$hero['views'] ?> views</p>
      <a class="btn btn-green shadow-sm" href="article.php?id=<?= (int)$hero['id'] ?>">Read full story &raquo;</a>
    </div>
  </div>
</div>
<?php else: ?>
  <div class="box text-center">No news has been published yet.</div>
<?php endif; ?>

<h3 class="section-title">Announcements</h3>
<?php foreach ($anns as $a): ?>
  <div class="alert alert-warning">
    <strong><?= e($a['title']) ?></strong><br>
    <?= e($a['body']) ?> <small class="text-muted">(<?= fmt_date($a['created_at']) ?>)</small>
  </div>
<?php endforeach; ?>
<?php if (!$anns): ?><p class="text-muted">No announcements right now.</p><?php endif; ?>

<h3 class="section-title">Latest News</h3>
<div class="row g-4">
  <?php
  foreach ($latest as $a) {
      $extra = '';
      if (!empty($a['subscribed'])) {
          $extra = '<div class="small text-success mt-1">From a category you follow</div>';
      }
      news_card($a, $base, $extra);
  }
  ?>
</div>
<?php if (!$latest): ?><p class="text-muted">No articles yet.</p><?php endif; ?>
<a href="news.php" class="btn btn-outline-success mt-3"><i class="bi bi-arrow-right me-1"></i>View all news</a>

<h3 class="section-title">Browse by Category</h3>
<?php foreach ($cats as $c): ?>
  <a class="btn btn-outline-success btn-sm mb-1" href="news.php?cat=<?= (int)$c['id'] ?>"><?= e($c['name']) ?> (<?= (int)$c['total'] ?>)</a>
<?php endforeach; ?>

<div class="box mt-5 d-flex justify-content-between align-items-center flex-wrap shadow-sm">
  <div>
    <h5 class="fw-bold mb-1" style="color: var(--nsbm-navy);">Only want some topics?</h5>
    <span class="text-muted">Subscribe to categories and see their news first on your home feed.</span>
  </div>
  <a class="btn btn-green shadow-sm" href="subscriptions.php"><i class="bi bi-bell me-1"></i>Manage Subscriptions</a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
