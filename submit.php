<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$cats = all_categories();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $title = post('title');
    $catId = (int)post('category_id');
    $body = post('body');
    $authorId = user()['id'];

    if (empty($title) || empty($body) || !$catId) {
        $error = 'Please fill in all required fields.';
    } else {
        $imgName = null;
        if (!empty($_FILES['image']['name'])) {
            $imgError = '';
            $imgName = save_image($_FILES['image'], $imgError);
        }
        $ins = $pdo->prepare('INSERT INTO articles (title, body, category_id, author_id, image, status) VALUES (?, ?, ?, ?, ?, "pending")');
        $ins->execute([$title, $body, $catId, $authorId, $imgName]);
        flash('success', 'Your article has been submitted for admin approval!');
        redirect('my-submissions.php');
    }
}

$pageTitle = 'Submit Article';
$active = 'submit';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="box p-4 shadow-sm">
        <h3 class="fw-bold mb-3">Submit News Article</h3>
        <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Headline</label>
            <input class="form-control" name="title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <select class="form-select" name="category_id" required>
              <option value="">Select Category</option>
              <?php foreach ($cats as $c): ?>
                <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Article Body</label>
            <textarea class="form-control" name="body" rows="6" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Cover Image</label>
            <input class="form-control" name="image" type="file" accept="image/*">
          </div>
          <button class="btn btn-green" type="submit">Submit for Review</button>
        </form>
      </div>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
