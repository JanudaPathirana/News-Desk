<?php
// approve or reject the articles sent by students
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $id = (int)post('id');
    $action = post('action');
    if ($action == 'approve' || $action == 'reject') {
        $newStatus = ($action == 'approve') ? 'approved' : 'rejected';
        $pdo->prepare("UPDATE articles SET status = ? WHERE id = ? AND status = 'pending'")->execute([$newStatus, $id]);
        if ($action == 'approve') {
            flash('success', '<strong>Article Approved!</strong> The article has been approved and is now live on the website.');
        } else {
            flash('danger', '<strong>Article Rejected!</strong> The article has been marked as Rejected.');
        }
    }
    redirect('pending.php');
}

// Page title and navigation setup
$adminTitle = 'Pending Approvals';
$adminActive = 'pending';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

$rows = $pdo->query("SELECT a.id, a.title, a.body, a.created_at, c.name AS cat, u.name AS author, u.student_id
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     JOIN users u ON u.id = a.author_id
                     WHERE a.status = 'pending' ORDER BY a.created_at ASC")->fetchAll();
?>
<?php foreach ($rows as $r): ?>
  <div class="box mb-3">
    <div class="d-flex flex-wrap justify-content-between gap-2">
      <div>
        <span class="badge bg-success"><?= e($r['cat']) ?></span>
        <h5 class="mt-2 mb-1"><?= e($r['title']) ?></h5>
        <small class="text-muted">By <?= e($r['author']) ?> (<?= e($r['student_id']) ?>) | <?= fmt_date($r['created_at']) ?></small>
      </div>
      <div class="d-flex gap-2 align-items-start">
        <a class="btn btn-sm btn-outline-dark" target="_blank" href="../article.php?id=<?= (int)$r['id'] ?>">Preview</a>
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <input type="hidden" name="action" value="approve">
          <button class="btn btn-sm btn-green">Approve</button>
        </form>
        <form method="post" onsubmit="return confirm('Reject this article?');">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <input type="hidden" name="action" value="reject">
          <button class="btn btn-sm btn-outline-danger">Reject</button>
        </form>
      </div>
    </div>
    <p class="mt-2 mb-0"><?= e(excerpt($r['body'], 300)) ?></p>
  </div>
<?php endforeach; ?>
<?php if (!$rows): ?><div class="box text-center">No articles are waiting for approval.</div><?php endif; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
