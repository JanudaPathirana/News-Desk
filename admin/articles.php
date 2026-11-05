<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// delete an article
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    if (post('action') == 'delete') {
        $id = (int)post('id');
        $st = $pdo->prepare('SELECT image FROM articles WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        if ($row) {
            $pdo->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
            delete_image($row['image']);
            flash('success', 'Article deleted.');
        }
    }
    redirect('articles.php');
}

// Page setup
$adminTitle = 'Articles';
$adminActive = 'articles';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

// search and filter
$status = $_GET['status'] ?? '';
$q = trim($_GET['q'] ?? '');
$where = '1 = 1';
$params = [];
if (in_array($status, ['pending', 'approved', 'rejected'])) {
    $where .= ' AND a.status = ?';
    $params[] = $status;
}
if ($q != '') {
    $where .= ' AND a.title LIKE ?';
    $params[] = "%$q%";
}

$st = $pdo->prepare("SELECT a.id, a.title, a.status, a.views, a.created_at, a.is_featured, a.is_breaking, c.name AS cat, u.name AS author
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     JOIN users u ON u.id = a.author_id
                     WHERE $where ORDER BY a.created_at DESC LIMIT 200");
$st->execute($params);
$rows = $st->fetchAll();
?>
<form method="get" class="box mb-3">
  <div class="row g-2 align-items-end">
    <div class="col-md-5">
      <label class="form-label" for="q">Search headline</label>
      <input class="form-control" id="q" name="q" value="<?= e($q) ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="status">Status</label>
      <select class="form-select" id="status" name="status">
        <option value="">All</option>
        <?php foreach (['approved', 'pending', 'rejected'] as $s): ?>
          <option value="<?= $s ?>" <?= $status == $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2"><button class="btn btn-green w-100">Filter</button></div>
    <div class="col-md-2"><a class="btn btn-light-green w-100" href="article_form.php">+ New</a></div>
  </div>
</form>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Headline</th><th>Category</th><th>Author</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <a href="../article.php?id=<?= (int)$r['id'] ?>" target="_blank"><?= e($r['title']) ?></a>
            <?php if ($r['is_featured']): ?><span class="badge bg-primary">Featured</span><?php endif; ?>
            <?php if ($r['is_breaking']): ?><span class="badge bg-warning text-dark">Breaking</span><?php endif; ?>
          </td>
          <td><?= e($r['cat']) ?></td>
          <td><?= e($r['author']) ?></td>
          <td><?= status_badge($r['status']) ?></td>
          <td><?= (int)$r['views'] ?></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td class="text-nowrap">
            <a class="btn btn-sm btn-outline-dark" href="article_form.php?id=<?= (int)$r['id'] ?>">Edit</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this article and its comments?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="7" class="text-center text-muted">No articles found.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
