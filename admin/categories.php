<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $name = post('name');
    if ($name) {
        $pdo->prepare('INSERT INTO categories (name) VALUES (?)')->execute([$name]);
        flash('success', 'Category added.');
    }
    redirect('categories.php');
}

$adminTitle = 'Categories';
$adminActive = 'cats';
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';

$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>
<div class="col-md-10 py-3">
  <h3>Manage Categories</h3>
  <form method="post" class="box mb-4">
    <?= csrf_field() ?>
    <div class="input-group">
      <input class="form-control" name="name" placeholder="New Category Name" required>
      <button class="btn btn-green">Add Category</button>
    </div>
  </form>
  <div class="box p-0">
    <table class="table mb-0">
      <thead><tr><th>ID</th><th>Category Name</th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
          <tr><td><?= (int)$c['id'] ?></td><td><?= e($c['name']) ?></td></tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
