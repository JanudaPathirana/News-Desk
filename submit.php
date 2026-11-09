<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$uid = user()['id'];
$editId = (int)($_GET['id'] ?? 0);
$art = null;

// edit mode: students can edit only their own pending / rejected articles
if ($editId) {
    $st = $pdo->prepare('SELECT * FROM articles WHERE id = ? AND author_id = ?');
    $st->execute([$editId, $uid]);
    $art = $st->fetch();
    if (!$art) {
        flash('danger', 'You cannot edit this article.');
        redirect('my-submissions.php');
    }
}

$cats = all_categories();
$old = [
    'title' => $art['title'] ?? '',
    'cat' => (int)($art['category_id'] ?? 0),
    'body' => $art['body'] ?? ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $old = ['title' => post('title'), 'cat' => (int)($_POST['category_id'] ?? 0), 'body' => post('body')];
    $errors = [];

    if (mb_strlen($old['title']) < 10 || mb_strlen($old['title']) > 200) {
        $errors[] = 'Headline must be 10 to 200 characters.';
    }
    if (mb_strlen($old['body']) < 50) {
        $errors[] = 'Article text must be at least 50 characters.';
    }
    if (!in_array($old['cat'], array_map('intval', array_column($cats, 'id')))) {
        $errors[] = 'Please choose a category.';
    }

    $img = save_image($_FILES['image'] ?? null, $imgError);
    if ($imgError) {
        $errors[] = $imgError;
    }

    if (!$errors) {
        if ($art) {
            // If already approved, it stays approved. If rejected, it moves to pending for re-review.
            $newStatus = ($art['status'] === 'rejected') ? 'pending' : $art['status'];
            $newImg = $img ? $img : $art['image'];
            $pdo->prepare("UPDATE articles SET title = ?, body = ?, category_id = ?, image = ?, status = ? WHERE id = ? AND author_id = ?")
                ->execute([$old['title'], $old['body'], $old['cat'], $newImg, $newStatus, $editId, $uid]);
            if ($img && $art['image'] && $art['image'] !== $img) {
                delete_image($art['image']);
            }

            if ($newStatus === 'approved') {
                flash('success', '<strong>Article Updated!</strong> Your article changes have been saved.');
            } elseif ($art['status'] === 'rejected') {
                flash('warning', '<strong>Article Resubmitted!</strong> Your changes were saved and submitted for Admin review.');
            } else {
                flash('warning', '<strong>Article Updated!</strong> Your changes were saved and are awaiting Admin review.');
            }
        } else {
            // new article submitted with pending status
            $pdo->prepare("INSERT INTO articles (title, body, category_id, author_id, image, status) VALUES (?, ?, ?, ?, ?, 'pending')")
                ->execute([$old['title'], $old['body'], $old['cat'], $uid, $img]);
            flash('warning', '<strong>Article Submitted!</strong> Your article is pending Admin approval. It will be published once approved.');
        }
        redirect('my-submissions.php');
    }

    // something is wrong, remove the uploaded image
    if ($img) {
        delete_image($img);
    }
    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

// Page title and navigation setup
$pageTitle = $art ? 'Edit Submission' : 'Submit Article';
$active = 'submit';

// Include header and navigation bar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<h1><?= $art ? 'Edit your article' : 'Submit an article' ?></h1>
<p class="text-muted">Students and clubs can send news here. An admin will check it before it is published.</p>

<form method="post" enctype="multipart/form-data" class="box">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label class="form-label" for="title">Headline</label>
    <input class="form-control" id="title" name="title" type="text" required minlength="10" maxlength="200" value="<?= e($old['title']) ?>">
  </div>
  <div class="mb-3">
    <label class="form-label" for="cat">Category</label>
    <select class="form-select" id="cat" name="category_id" required>
      <option value="">Choose...</option>
      <?php foreach ($cats as $c): ?>
        <option value="<?= (int)$c['id'] ?>" <?= $old['cat'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3">
    <label class="form-label" for="body">Article text</label>
    <textarea class="form-control" id="body" name="body" rows="7" required minlength="50"><?= e($old['body']) ?></textarea>
  </div>
  <div class="mb-3">
    <label class="form-label" for="image">Cover image (optional, max 2MB)</label>
    <input class="form-control" type="file" id="image" name="image" accept="image/*">
    <?php if ($art && $art['image']): ?>
      <small class="text-muted">The old image stays if you do not choose a new one.</small>
    <?php endif; ?>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-green"><?= $art ? 'Save Changes' : 'Submit for approval' ?></button>
    <a class="btn btn-outline-dark" href="my-submissions.php">Cancel</a>
  </div>
</form>

<?php if ($art): ?>
  <div class="box mt-4 border-danger">
    <h5 class="text-danger mb-2">Danger Zone</h5>
    <p class="text-muted small mb-2">Once you delete an article, it cannot be recovered.</p>
    <form method="post" action="my-submissions.php" onsubmit="return confirm('Are you sure you want to permanently delete this article?');">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
      <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Delete this article</button>
    </form>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
