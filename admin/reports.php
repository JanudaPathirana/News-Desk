<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = "a.status = 'approved'";
$params = [];
if ($from) { $where .= ' AND DATE(a.created_at) >= ?'; $params[] = $from; }
if ($to) { $where .= ' AND DATE(a.created_at) <= ?'; $params[] = $to; }

$st = $pdo->prepare("SELECT a.id, a.title, c.name AS cat, a.views, a.created_at
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE $where ORDER BY a.views DESC");
$st->execute($params);
$rows = $st->fetchAll();

$adminTitle = 'Readership Reports';
$adminActive = 'reports';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';
?>
<div class="col-md-10 py-3">
  <h3>Campus Readership Analytics</h3>
  <div class="box p-0 mb-4">
    <table class="table mb-0">
      <thead><tr><th>Title</th><th>Category</th><th>Views</th><th>Published Date</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr><td><?= e($r['title']) ?></td><td><?= e($r['cat']) ?></td><td><?= (int)$r['views'] ?></td><td><?= fmt_date($r['created_at']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
