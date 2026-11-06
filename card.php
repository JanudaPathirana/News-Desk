<?php
// news card used in home, news and saved pages
// $a = article row, $extra = extra html at the bottom of the card
function news_card($a, $base = '', $extra = '') { ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 border-0 shadow-sm article-card">
      <div class="position-relative overflow-hidden card-img-wrap">
        <a href="<?= $base ?>article.php?id=<?= (int)$a['id'] ?>">
          <img class="card-img-top news-img" src="<?= img_url($a['image'], $base) ?>" alt="<?= e($a['title']) ?>">
        </a>
        <div class="position-absolute top-0 start-0 m-3 d-flex gap-1 flex-wrap">
          <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold"><?= e($a['cat']) ?></span>
          <?php if (!empty($a['is_breaking'])): ?>
            <span class="badge bg-danger shadow-sm"><i class="bi bi-lightning-fill me-1"></i>Breaking</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="card-body d-flex flex-column p-4">
        <h5 class="card-title fw-bold mb-2">
          <a href="<?= $base ?>article.php?id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a>
        </h5>
        <p class="card-text text-muted mb-3 flex-grow-1"><?= e(excerpt($a['body'], 115)) ?></p>
        <div class="pt-3 border-top d-flex justify-content-between align-items-center text-muted small">
          <span><i class="bi bi-calendar3 me-1 text-primary"></i><?= fmt_date($a['created_at']) ?></span>
          <span><i class="bi bi-eye me-1 text-success"></i><?= (int)$a['views'] ?> views</span>
        </div>
        <?= $extra ?>
      </div>
    </div>
  </div>
<?php }
