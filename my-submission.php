<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$uid = user()['id'];

// delete a submission (student can delete any of their own submissions)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    if (post('action') == 'delete') {
        $id = (int)post('id');
        $st = $pdo->prepare("SELECT image FROM articles WHERE id = ? AND author_id = ?");
        $st->execute([$id, $uid]);
        $row = $st->fetch();
        if ($row) {
            $pdo->prepare('DELETE FROM articles WHERE id = ? AND author_id = ?')->execute([$id, $uid]);
            delete_image($row['image']);
            flash('success', '<strong>Article Deleted!</strong> Your article has been successfully deleted.');
        } else {
            flash('danger', 'You do not have permission to delete this article or it was not found.');
        }
    }
    redirect('my-submissions.php');
}

$st = $pdo->prepare('SELECT a.id, a.title, a.status, a.created_at, a.views, c.name AS cat
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE a.author_id = ? ORDER BY a.created_at DESC');
$st->execute([$uid]);
$rows = $st->fetchAll();

// Page title and navigation setup
$pageTitle = 'My Submissions';
$active = 'submit';

// Include header and navigation bar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$approvedCount = 0;
$rejectedCount = 0;
$pendingCount = 0;
foreach ($rows as $r) {
    if ($r['status'] === 'approved') $approvedCount++;
    elseif ($r['status'] === 'rejected') $rejectedCount++;
    elseif ($r['status'] === 'pending') $pendingCount++;
}
?>

<?php if ($approvedCount > 0): ?>
  <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
    <i class="bi bi-check-circle-fill fs-5"></i>
    <div class="flex-grow-1">
      <strong>Article Approved!</strong> You have <strong><?= $approvedCount ?></strong> article(s) approved by the Admin and published live.
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($rejectedCount > 0): ?>
  <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
    <i class="bi bi-x-circle-fill fs-5"></i>
    <div class="flex-grow-1">
      <strong>Article Rejected!</strong> You have <strong><?= $rejectedCount ?></strong> article(s) rejected by the Admin. Click <strong>Edit</strong> to revise and resubmit.
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if ($pendingCount > 0): ?>
  <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm" role="alert">
    <i class="bi bi-hourglass-split fs-5"></i>
    <div class="flex-grow-1">
      <strong>Pending Approval:</strong> You have <strong><?= $pendingCount ?></strong> article(s) awaiting Admin approval.
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="mb-0">My Submissions</h1>
  <a class="btn btn-green" href="submit.php">+ New Article</a>
</div>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Headline</th><th>Category</th><th>Status</th><th>Submitted</th><th>Views</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><a href="article.php?id=<?= (int)$r['id'] ?>"><?= e($r['title']) ?></a></td>
          <td><?= e($r['cat']) ?></td>
          <td><?= status_badge($r['status']) ?></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td><?= (int)$r['views'] ?></td>
          <td>
            <a class="btn btn-sm btn-outline-success" href="submit.php?id=<?= (int)$r['id'] ?>"><i class="bi bi-pencil me-1"></i>Edit</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this submission?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="text-center text-muted">You have not submitted anything yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
