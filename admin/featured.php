<?php
// turn featured / breaking on and off for published articles
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $id = (int)post('id');
    $flag = post('flag');

    // only these two columns are allowed
    if ($flag == 'featured') {
        $pdo->prepare("UPDATE articles SET is_featured = 1 - is_featured WHERE id = ? AND status = 'approved'")->execute([$id]);
        flash('success', 'Updated.');
    } elseif ($flag == 'breaking') {
        $pdo->prepare("UPDATE articles SET is_breaking = 1 - is_breaking WHERE id = ? AND status = 'approved'")->execute([$id]);
        flash('success', 'Updated.');
    }
    redirect('featured.php');
}

// Page title and navigation setup
$adminTitle = 'Featured / Breaking News';
$adminActive = 'featured';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

$rows = $pdo->query("SELECT a.id, a.title, a.is_featured, a.is_breaking, a.created_at, c.name AS cat
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE a.status = 'approved'
                     ORDER BY a.is_breaking DESC, a.is_featured DESC, a.created_at DESC")->fetchAll();
?>
<p class="text-muted">The main news on the home page is the breaking news first, then featured, then the latest article.</p>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Headline</th><th>Category</th><th>Date</th><th>Featured</th><th>Breaking</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e($r['title']) ?></td>
          <td><?= e($r['cat']) ?></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td>
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="flag" value="featured">
              <button class="btn btn-sm <?= $r['is_featured'] ? 'btn-green' : 'btn-outline-dark' ?>"><?= $r['is_featured'] ? 'On' : 'Off' ?></button>
            </form>
          </td>
          <td>
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="flag" value="breaking">
              <button class="btn btn-sm <?= $r['is_breaking'] ? 'btn-green' : 'btn-outline-dark' ?>"><?= $r['is_breaking'] ? 'On' : 'Off' ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="5" class="text-center text-muted">No published articles.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
