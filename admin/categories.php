<?php
// add, rename and delete categories
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $action = post('action');
    $name = post('name');
    $id = (int)post('id');

    if ($action == 'add' || $action == 'edit') {
        if (mb_strlen($name) < 3 || mb_strlen($name) > 60) {
            flash('danger', 'Category name must be 3 to 60 characters.');
        } else {
            // check if the name is already used
            $dup = $pdo->prepare('SELECT id FROM categories WHERE name = ? AND id <> ?');
            $dup->execute([$name, $id]);
            if ($dup->fetchColumn()) {
                flash('danger', 'This category already exists.');
            } elseif ($action == 'add') {
                $pdo->prepare('INSERT INTO categories (name) VALUES (?)')->execute([$name]);
                flash('success', 'Category added.');
            } else {
                $pdo->prepare('UPDATE categories SET name = ? WHERE id = ?')->execute([$name, $id]);
                flash('success', 'Category renamed.');
            }
        }
    } elseif ($action == 'delete') {
        // cannot delete if articles are using it
        $n = $pdo->prepare('SELECT COUNT(*) FROM articles WHERE category_id = ?');
        $n->execute([$id]);
        if ((int)$n->fetchColumn() > 0) {
            flash('danger', 'This category still has articles. Move or delete them first.');
        } else {
            $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            flash('success', 'Category deleted.');
        }
    }
    redirect('categories.php');
}

// Page title and navigation setup
$adminTitle = 'Categories';
$adminActive = 'cats';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

$rows = $pdo->query('SELECT c.id, c.name, COUNT(a.id) AS total FROM categories c
                     LEFT JOIN articles a ON a.category_id = c.id
                     GROUP BY c.id, c.name ORDER BY c.name')->fetchAll();
?>
<form method="post" class="box mb-3">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="add">
  <label class="form-label" for="newcat">Add a category</label>
  <div class="input-group">
    <input class="form-control" id="newcat" name="name" required minlength="3" maxlength="60" placeholder="e.g. Clubs">
    <button class="btn btn-green">Add</button>
  </div>
</form>

<div class="box table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead class="table-success">
      <tr><th>Name</th><th>Articles</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td>
            <form method="post" class="d-flex gap-2">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input class="form-control form-control-sm" name="name" value="<?= e($r['name']) ?>" required minlength="3" maxlength="60">
              <button class="btn btn-sm btn-outline-dark">Rename</button>
            </form>
          </td>
          <td><?= (int)$r['total'] ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Delete this category?');">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
