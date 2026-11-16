<?php
// Set page title and active navigation item
$pageTitle = 'All News';
$active = 'news';

// Include header, navigation bar, and news card components
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/card.php';

// filter values from the url
$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);
$from = $_GET['from'] ?? '';
if ($from != '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
    $from = '';
}

// build the where part of the query
$where = "a.status = 'approved'";
$params = [];
if ($q != '') {
    $where .= " AND (a.title LIKE ? OR a.body LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($cat > 0) {
    $where .= " AND a.category_id = ?";
    $params[] = $cat;
}
if ($from != '') {
    $where .= " AND DATE(a.created_at) >= ?";
    $params[] = $from;
}

// pagination (9 per page)
$perPage = 9;
$page = max(1, (int)($_GET['page'] ?? 1));

$count = $pdo->prepare("SELECT COUNT(*) FROM articles a WHERE $where");
$count->execute($params);
$total = (int)$count->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
if ($page > $pages) {
    $page = $pages;
}
$offset = ($page - 1) * $perPage;

$st = $pdo->prepare("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE $where ORDER BY a.created_at DESC LIMIT $perPage OFFSET $offset");
$st->execute($params);
$rows = $st->fetchAll();
$cats = all_categories();

// link for pagination buttons (keeps the search values)
function page_link($p) {
    $query = $_GET;
    $query['page'] = $p;
    return 'news.php?' . http_build_query($query);
}
?>
<h1 class="mb-3 fw-bold" style="color: var(--nsbm-navy);">All News</h1>

<form method="get" class="box mb-4 no-print shadow-sm">
  <div class="row g-3 align-items-end">
    <div class="col-md-5">
      <label class="form-label fw-semibold" for="q">Search Headline / Body</label>
      <input class="form-control" id="q" name="q" value="<?= e($q) ?>" placeholder="Search news, topics...">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-semibold" for="cat">Category</label>
      <select class="form-select" id="cat" name="cat">
        <option value="0">All categories</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label fw-semibold" for="from">Published after</label>
      <input type="date" class="form-control" id="from" name="from" value="<?= e($from) ?>">
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button class="btn btn-green flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
      <a class="btn btn-outline-secondary" href="news.php">Reset</a>
    </div>
  </div>
</form>

<p class="text-muted"><?= $total ?> article(s) found</p>
<div class="row g-4">
  <?php foreach ($rows as $a) { news_card($a, $base); } ?>
</div>
<?php if (!$rows): ?><div class="box text-center">No articles match your search.</div><?php endif; ?>

<?php if ($pages > 1): ?>
<nav class="mt-4 no-print">
  <ul class="pagination justify-content-center">
    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= page_link($page - 1) ?>">Previous</a></li>
    <?php for ($i = 1; $i <= $pages; $i++): ?>
      <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="<?= page_link($i) ?>"><?= $i ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= page_link($page + 1) ?>">Next</a></li>
  </ul>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
