<?php
// Student article submission (status = 'pending' until admin approves)
require_once __DIR__ . '/includes/auth.php';
require_login();

$cats = all_categories();

$f = ['title' => '', 'body' => '', 'cat' => 0];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $f = [
        'title' => post('title'),
        'body'  => post('body'),
        'cat'   => (int)($_POST['category_id'] ?? 0)
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

    $img = save_image($_FILES['image'] ?? null, $imgError);
    if ($imgError) {
        $errors[] = $imgError;
    }

    if (!$errors) {
        $pdo->prepare('INSERT INTO articles (title, body, category_id, author_id, status, image) VALUES (?, ?, ?, ?, ?, ?)')
            ->execute([$f['title'], $f['body'], $f['cat'], user()['id'], 'pending', $img]);
        flash('success', 'Your article has been submitted and is awaiting admin approval.');
        redirect('news.php');
    }

    if ($img) {
        delete_image($img);
    }
    foreach ($errors as $err) {
        flash('danger', $err);
    }
}

$pageTitle = 'Submit Article';
$active = 'submit';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="box shadow-sm">
      <div class="text-center mb-4">
        <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success mb-2">
          <i class="bi bi-pencil-square fs-3"></i>
        </div>
        <h3 class="fw-bold mb-1" style="color: var(--nsbm-navy);">Submit an Article</h3>
        <p class="text-muted small">Your article will be reviewed by an admin before it is published.</p>
      </div>
      <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label class="form-label fw-semibold" for="title">Headline</label>
          <input class="form-control" id="title" name="title" required minlength="10" maxlength="200" placeholder="e.g. Faculty of Computing Hosts Annual Hackathon" value="<?= e($f['title']) ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold" for="cat">Category</label>
          <select class="form-select" id="cat" name="category_id" required>
            <option value="">Choose a category...</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= $f['cat'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold" for="body">Article Text</label>
          <textarea class="form-control" id="body" name="body" rows="10" required minlength="50" placeholder="Write your article here (minimum 50 characters)..."><?= e($f['body']) ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold" for="image">Cover Image (optional, max 2MB)</label>
          <input class="form-control" type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
        </div>
        <div class="d-flex justify-content-between align-items-center">
          <a href="news.php" class="btn btn-outline-secondary">Cancel</a>
          <button class="btn btn-green px-4 py-2 shadow-sm"><i class="bi bi-send me-1"></i>Submit for Review</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
