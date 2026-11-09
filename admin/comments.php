<?php
// comment moderation: hide, show or delete comments
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $id = (int)post('id');
    $action = post('action');

    if ($action == 'hide' || $action == 'show') {
        $newStatus = ($action == 'hide') ? 'hidden' : 'visible';
        $pdo->prepare('UPDATE comments SET status = ? WHERE id = ?')->execute([$newStatus, $id]);
        flash('success', 'Comment updated.');
    } elseif ($action == 'delete') {
        $pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
        flash('success', 'Comment deleted.');
    }
    redirect('comments.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
}

// Page title and navigation setup
$adminTitle = 'Comment Moderation';
$adminActive = 'comments';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

// filter by status
$status = $_GET['status'] ?? '';
$where = '';
$params = [];
if ($status == 'visible' || $status == 'hidden') {
    $where = 'WHERE m.status = ?';
    $params[] = $status;
}
$st = $pdo->prepare("SELECT m.id, m.body, m.status, m.created_at, u.name, a.id AS aid, a.title
                     FROM comments m
                     JOIN users u ON u.id = m.user_id
                     JOIN articles a ON a.id = m.article_id
                     $where ORDER BY m.created_at DESC LIMIT 200");
$st->execute($params);
$rows = $st->fetchAll();
?>
<div class="mb-3">
  <a class="btn btn-sm <?= $status == '' ? 'btn-green' : 'btn-outline-dark' ?>" href="comments.php">All</a>
  <a class="btn btn-sm <?= $status == 'visible' ? 'btn-green' : 'btn-outline-dark' ?>" href="comments.php?status=visible">Visible</a>
  <a class="btn btn-sm <?= $status == 'hidden' ? 'btn-green' : 'btn-outline-dark' ?>" href="comments.php?status=hidden">Hidden</a>
</div>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Comment</th><th>By</th><th>Article</th><th>Status</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= e(excerpt($r['body'], 160)) ?></td>
          <td><?= e($r['name']) ?></td>
          <td><a href="../article.php?id=<?= (int)$r['aid'] ?>" target="_blank"><?= e(excerpt($r['title'], 40)) ?></a></td>
          <td><?= status_badge($r['status']) ?></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td class="text-nowrap">
            <form method="post" class="d-inline">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="<?= $r['status'] == 'visible' ? 'hide' : 'show' ?>">
              <button class="btn btn-sm btn-outline-dark"><?= $r['status'] == 'visible' ? 'Hide' : 'Show' ?></button>
            </form>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this comment?');">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="delete">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6" class="text-center text-muted">No comments.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
