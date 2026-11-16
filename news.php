<?php
$pageTitle = 'All News';
$active = 'news';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
require_once __DIR__ . '/includes/card.php';

$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['cat'] ?? 0);

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

$st = $pdo->prepare("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE $where ORDER BY a.created_at DESC LIMIT 12");
$st->execute($params);
$rows = $st->fetchAll();
$cats = all_categories();
?>
<main class="container my-5">
  <div class="box p-3 mb-4 shadow-sm">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-md-6">
        <input class="form-control" name="q" placeholder="Search news by headline or keyword..." value="<?= e($q) ?>">
      </div>
      <div class="col-md-4">
        <select class="form-select" name="cat">
          <option value="0">All Categories</option>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-green w-100" type="submit">Filter</button>
      </div>
    </form>
  </div>
  <div class="row g-4">
    <?php foreach ($rows as $a): news_card($a); endforeach; ?>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
