<?php
// readership report (date filter, csv download and print)
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$pattern = '/^\d{4}-\d{2}-\d{2}$/';
if (!preg_match($pattern, $from)) {
    $from = '';
}
if (!preg_match($pattern, $to)) {
    $to = '';
}

$where = "a.status = 'approved'";
$params = [];
if ($from != '') {
    $where .= ' AND DATE(a.created_at) >= ?';
    $params[] = $from;
}
if ($to != '') {
    $where .= ' AND DATE(a.created_at) <= ?';
    $params[] = $to;
}

$st = $pdo->prepare("SELECT a.id, a.title, c.name AS cat, a.views, a.created_at,
        (SELECT COUNT(*) FROM comments m WHERE m.article_id = a.id AND m.status = 'visible') AS comments,
        (SELECT COUNT(*) FROM bookmarks b WHERE b.article_id = a.id) AS bookmarks
     FROM articles a JOIN categories c ON c.id = a.category_id
     WHERE $where ORDER BY a.views DESC");
$st->execute($params);
$rows = $st->fetchAll();

// csv download (must be before any html)
if (($_GET['export'] ?? '') == 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="newsdesk-report-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Headline', 'Category', 'Published', 'Views', 'Comments', 'Bookmarks']);
    foreach ($rows as $r) {
        fputcsv($out, [$r['title'], $r['cat'], $r['created_at'], $r['views'], $r['comments'], $r['bookmarks']]);
    }
    exit;
}

// Page title and navigation setup
$adminTitle = 'Readership Reports';
$adminActive = 'reports';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

// totals
$totalViews = array_sum(array_column($rows, 'views'));
$totalComments = array_sum(array_column($rows, 'comments'));
$totalBookmarks = array_sum(array_column($rows, 'bookmarks'));
$maxViews = max(1, (int)($rows[0]['views'] ?? 1));

// per category numbers
$catRows = $pdo->query("SELECT c.name, COUNT(a.id) AS articles, COALESCE(SUM(a.views), 0) AS views,
        (SELECT COUNT(*) FROM subscriptions s WHERE s.category_id = c.id) AS subscribers
     FROM categories c LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'approved'
     GROUP BY c.id, c.name ORDER BY views DESC")->fetchAll();

$qs = http_build_query(array_filter(['from' => $from, 'to' => $to]));
?>
<form method="get" class="box mb-4 no-print">
  <div class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label" for="from">Published from</label>
      <input type="date" class="form-control" id="from" name="from" value="<?= e($from) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="to">Published to</label>
      <input type="date" class="form-control" id="to" name="to" value="<?= e($to) ?>">
    </div>
    <div class="col-md-2"><button class="btn btn-green w-100">Apply</button></div>
    <div class="col-md-2"><a class="btn btn-outline-dark w-100" href="reports.php?export=csv&amp;<?= $qs ?>">CSV</a></div>
    <div class="col-md-2"><button type="button" class="btn btn-light-green w-100" onclick="window.print()">Print</button></div>
  </div>
</form>

<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="box text-center"><div class="big-number"><?= count($rows) ?></div>Articles</div></div>
  <div class="col-6 col-md-3"><div class="box text-center"><div class="big-number"><?= number_format($totalViews) ?></div>Total views</div></div>
  <div class="col-6 col-md-3"><div class="box text-center"><div class="big-number"><?= number_format($totalComments) ?></div>Comments</div></div>
  <div class="col-6 col-md-3"><div class="box text-center"><div class="big-number"><?= number_format($totalBookmarks) ?></div>Bookmarks</div></div>
</div>

<h5>By category</h5>
<div class="box table-responsive mb-4">
  <table class="table table-striped mb-0">
    <thead class="table-success">
      <tr><th>Category</th><th>Articles</th><th>Views</th><th>Subscribers</th></tr>
    </thead>
    <tbody>
      <?php foreach ($catRows as $c): ?>
        <tr>
          <td><?= e($c['name']) ?></td>
          <td><?= (int)$c['articles'] ?></td>
          <td><?= number_format((int)$c['views']) ?></td>
          <td><?= (int)$c['subscribers'] ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<h5>By article</h5>
<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Headline</th><th>Category</th><th>Published</th><th>Views</th><th>Comments</th><th>Bookmarks</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['title']) ?></td>
          <td><?= e($r['cat']) ?></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td>
            <?= (int)$r['views'] ?>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: <?= round($r['views'] / $maxViews * 100) ?>%"></div>
            </div>
          </td>
          <td><?= (int)$r['comments'] ?></td>
          <td><?= (int)$r['bookmarks'] ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">No published articles in this range.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
