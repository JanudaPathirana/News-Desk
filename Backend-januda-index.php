<?php
$pageTitle = 'Home';
$active = 'home';
require_once __DIR__ . '/includes/header.php';
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
    <input class="form-control rounded-pill" type="text" name="q" placeholder="Search news...">
    <button class="btn btn-light-green">Search</button>
  </form>
  <a class="btn btn-outline-light btn-sm" href="submit.php">Submit an article</a>
</section>

<?php if ($hero): ?>
<div class="box">
  <div class="row">
    <div class="col-md-5">
      <img class="hero-img" src="<?= img_url($hero['image'], $base) ?>" alt="main news">
    </div>
    <div class="col-md-7">
      <?php if ($hero['is_breaking']): ?><span class="badge bg-warning text-dark">Breaking</span><?php endif; ?>
      <?php if ($hero['is_featured']): ?><span class="badge bg-success">Featured</span><?php endif; ?>
      <span class="badge bg-secondary"><?= e($hero['cat']) ?></span>
      <h2 class="mt-2"><?= e($hero['title']) ?></h2>
      <p><?= e(excerpt($hero['body'], 180)) ?></p>
      <p class="text-muted"><?= fmt_date($hero['created_at']) ?> | <?= (int)$hero['views'] ?> views</p>
      <a class="btn btn-green" href="article.php?id=<?= (int)$hero['id'] ?>">Read full story</a>
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
<a href="news.php" class="btn btn-outline-dark mt-3">View all news</a>

<h3 class="section-title">Browse by Category</h3>
<?php foreach ($cats as $c): ?>
  <a class="btn btn-outline-success btn-sm mb-1" href="news.php?cat=<?= (int)$c['id'] ?>"><?= e($c['name']) ?> (<?= (int)$c['total'] ?>)</a>
<?php endforeach; ?>

<div class="box mt-5 d-flex justify-content-between align-items-center flex-wrap">
  <div>
    <h5>Only want some topics?</h5>
    <span class="text-muted">Subscribe to categories and see their news first.</span>
  </div>
  <a class="btn btn-light-green" href="subscriptions.php">Manage Subscriptions</a>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
