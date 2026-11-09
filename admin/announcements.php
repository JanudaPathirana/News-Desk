<?php
// add, edit, hide and delete announcements
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $action = post('action');
    $id = (int)post('id');

    if ($action == 'save') {
        $title = post('title');
        $body = post('body');
        $active = isset($_POST['is_active']) ? 1 : 0;

        if (mb_strlen($title) < 5 || mb_strlen($title) > 200 || mb_strlen($body) < 10) {
            flash('danger', 'Title needs 5+ characters and the message needs 10+ characters.');
            redirect('announcements.php' . ($id ? '?edit=' . $id : ''));
        }
        if ($id) {
            $pdo->prepare('UPDATE announcements SET title = ?, body = ?, is_active = ? WHERE id = ?')->execute([$title, $body, $active, $id]);
            flash('success', 'Announcement updated.');
        } else {
            $pdo->prepare('INSERT INTO announcements (title, body, is_active) VALUES (?, ?, ?)')->execute([$title, $body, $active]);
            flash('success', 'Announcement published.');
        }
    } elseif ($action == 'toggle') {
        $pdo->prepare('UPDATE announcements SET is_active = 1 - is_active WHERE id = ?')->execute([$id]);
    } elseif ($action == 'delete') {
        $pdo->prepare('DELETE FROM announcements WHERE id = ?')->execute([$id]);
        flash('success', 'Announcement deleted.');
    }
    redirect('announcements.php');
}

// Page title and navigation setup
$adminTitle = 'Announcements';
$adminActive = 'ann';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

// if edit button was clicked load that announcement
$edit = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare('SELECT * FROM announcements WHERE id = ?');
    $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch();
}
$rows = $pdo->query('SELECT * FROM announcements ORDER BY created_at DESC')->fetchAll();
?>
<form method="post" class="box mb-4">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save">
  <input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <h5><?= $edit ? 'Edit announcement' : 'New announcement' ?></h5>
  <div class="mb-3">
    <label class="form-label" for="title">Title</label>
    <input class="form-control" id="title" name="title" required minlength="5" maxlength="200" value="<?= e($edit['title'] ?? '') ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="body">Message</label>
    <textarea class="form-control" id="body" name="body" rows="3" required minlength="10"><?= e($edit['body'] ?? '') ?></textarea>
  </div>
  <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" id="act" name="is_active" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>>
    <label class="form-check-label" for="act">Show on the home page</label>
  </div>
  <button class="btn btn-green"><?= $edit ? 'Save Changes' : 'Publish' ?></button>
  <?php if ($edit): ?><a class="btn btn-outline-dark" href="announcements.php">Cancel</a><?php endif; ?>
</form>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Title</th><th>Date</th><th>Visible</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><strong><?= e($r['title']) ?></strong><br><small class="text-muted"><?= e(excerpt($r['body'], 90)) ?></small></td>
          <td><?= fmt_date($r['created_at']) ?></td>
          <td>
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm <?= $r['is_active'] ? 'btn-green' : 'btn-outline-dark' ?>"><?= $r['is_active'] ? 'Shown' : 'Hidden' ?></button>
            </form>
          </td>
          <td class="text-nowrap">
            <a class="btn btn-sm btn-outline-dark" href="announcements.php?edit=<?= (int)$r['id'] ?>">Edit</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="4" class="text-center text-muted">No announcements yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
