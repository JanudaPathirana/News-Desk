<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $id = (int)post('id');
    $action = post('action');
    if ($action == 'approve' || $action == 'reject') {
        $status = ($action == 'approve') ? 'approved' : 'rejected';
        $pdo->prepare("UPDATE articles SET status = ? WHERE id = ?")->execute([$status, $id]);
        flash('success', 'Article status updated to ' . $status);
    }
    redirect('pending.php');
}

$adminTitle = 'Pending Approvals';
$adminActive = 'pending';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

$rows = $pdo->query("SELECT a.id, a.title, c.name as cat, u.name as author FROM articles a JOIN categories c ON c.id = a.category_id JOIN users u ON u.id = a.author_id WHERE a.status = 'pending'")->fetchAll();
?>
<div class="col-md-10 py-3">
  <h3>Articles Waiting Approval</h3>
  <?php foreach ($rows as $r): ?>
    <div class="box mb-3 d-flex justify-content-between align-items-center">
      <div><strong><?= e($r['title']) ?></strong> &bull; <span class="badge bg-success"><?= e($r['cat']) ?></span> by <?= e($r['author']) ?></div>
      <form method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button name="action" value="approve" class="btn btn-sm btn-green">Approve</button>
        <button name="action" value="reject" class="btn btn-sm btn-outline-danger">Reject</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
