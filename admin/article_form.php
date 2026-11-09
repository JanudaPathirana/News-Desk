<?php
// add new article or edit an old one (admin)
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$art = null;
if ($id) {
    $st = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
    $st->execute([$id]);
    $art = $st->fetch();
    if (!$art) {
        flash('danger', 'Article not found.');
        redirect('articles.php');
    }
}
$cats = all_categories();

// values shown in the form
$f = [
    'title' => $art['title'] ?? '',
    'body' => $art['body'] ?? '',
    'cat' => (int)($art['category_id'] ?? 0),
    'status' => $art['status'] ?? 'approved',
    'feat' => (int)($art['is_featured'] ?? 0),
    'break' => (int)($art['is_breaking'] ?? 0)
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $f = [
        'title' => post('title'),
        'body' => post('body'),
        'cat' => (int)($_POST['category_id'] ?? 0),
        'status' => post('status'),
        'feat' => isset($_POST['is_featured']) ? 1 : 0,
        'break' => isset($_POST['is_breaking']) ? 1 : 0
    ];
    $errors = [];

    if (mb_strlen($f['title']) < 10 || mb_strlen($f['title']) > 200) {
        $errors[] = 'Headline must be 10 to 200 characters.';
    }
    if (mb_strlen($f['body']) < 50) {
        $errors[] = 'Article text must be at least 50 characters.';
    }
    if (!in_array($f['cat'], array_map('intval', array_column($cats, 'id')))) {
        $errors[] = 'Please choose a category.';
    }
    if (!in_array($f['status'], ['pending', 'approved', 'rejected'])) {
        $errors[] = 'Wrong status.';
    }

    $img = save_image($_FILES['image'] ?? null, $imgError);
    if ($imgError) {
        $errors[] = $imgError;
    }

    if (!$errors) {
        if ($art) {
            $newImg = $img ? $img : $art['image'];
            $pdo->prepare('UPDATE articles SET title = ?, body = ?, category_id = ?, status = ?, is_featured = ?, is_breaking = ?, image = ? WHERE id = ?')
                ->execute([$f['title'], $f['body'], $f['cat'], $f['status'], $f['feat'], $f['break'], $newImg, $id]);
            if ($img) {
                delete_image($art['image']);
            }
            flash('success', 'Article updated.');
        } else {
            $pdo->prepare('INSERT INTO articles (title, body, category_id, author_id, status, is_featured, is_breaking, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
                ->execute([$f['title'], $f['body'], $f['cat'], user()['id'], $f['status'], $f['feat'], $f['break'], $img]);
            flash('success', 'Article created.');
        }
        redirect('articles.php');
    }

    if ($img) {
        delete_image($img);
    }
    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

// Page title and navigation setup
$adminTitle = $art ? 'Edit Article' : 'New Article';
$adminActive = 'articles';

// Include admin header and navigation bar
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/admin_navbar.php';
?>
<form method="post" enctype="multipart/form-data" class="box">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label" for="title">Headline</label>
    <input class="form-control" id="title" name="title" required minlength="10" maxlength="200" value="<?= e($f['title']) ?>">
  </div>
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label class="form-label" for="cat">Category</label>
      <select class="form-select" id="cat" name="category_id" required>
        <option value="">Choose...</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int)$c['id'] ?>" <?= $f['cat'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="status">Status</label>
      <select class="form-select" id="status" name="status">
        <option value="approved" <?= $f['status'] == 'approved' ? 'selected' : '' ?>>Approved (published)</option>
        <option value="pending" <?= $f['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="rejected" <?= $f['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>
  </div>
  <div class="mb-3">
    <label class="form-label" for="body">Article text</label>
    <textarea class="form-control" id="body" name="body" rows="10" required minlength="50"><?= e($f['body']) ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="image">Cover image (optional, max 2MB)</label>
    <input class="form-control" type="file" id="image" name="image" accept="image/*">
    <?php if ($art && $art['image']): ?>
      <img class="mt-2 rounded" src="<?= img_url($art['image'], '../') ?>" alt="current image" height="100">
    <?php endif; ?>
  </div>
  <div class="mb-3">
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" id="feat" name="is_featured" <?= $f['feat'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="feat">Featured</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" id="brk" name="is_breaking" <?= $f['break'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="brk">Breaking news</label>
    </div>
  </div>
  <button class="btn btn-green"><?= $art ? 'Save Changes' : 'Publish Article' ?></button>
  <a class="btn btn-outline-dark" href="articles.php">Cancel</a>
</form>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
