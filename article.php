<?php
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT a.*, c.name AS cat, u.name AS author_name, u.profile_photo AS author_photo
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     JOIN users u ON u.id = a.author_id
                     WHERE a.id = ? AND a.status = 'approved'");
$st->execute([$id]);
$article = $st->fetch();

if (!$article) {
    flash('danger', 'Article not found or pending approval.');
    redirect('news.php');
}

// Increment view count
$pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = ?')->execute([$id]);

// Handle bookmark toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && is_logged_in()) {
    csrf_check();
    $uid = user()['id'];
    $action = post('action');

    if ($action == 'bookmark') {
        $chk = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id = ? AND article_id = ?');
        $chk->execute([$uid, $id]);
        if ($chk->fetchColumn()) {
            $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?')->execute([$uid, $id]);
            flash('success', 'Article removed from saved.');
        } else {
            $pdo->prepare('INSERT INTO bookmarks (user_id, article_id) VALUES (?, ?)')->execute([$uid, $id]);
            flash('success', 'Article saved to your bookmarks!');
        }
    } elseif ($action == 'comment') {
        $body = post('comment_body');
        if (mb_strlen($body) < 3) {
            flash('danger', 'Comment must be at least 3 characters.');
        } elseif (mb_strlen($body) > 1000) {
            flash('danger', 'Comment must be less than 1000 characters.');
        } else {
            $pdo->prepare('INSERT INTO comments (article_id, user_id, body) VALUES (?, ?, ?)')
                ->execute([$id, $uid, $body]);
            flash('success', 'Your comment has been posted.');
        }
    }
    redirect('article.php?id=' . $id);
}

// Check if bookmarked
$isBookmarked = false;
if (is_logged_in()) {
    $bk = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id = ? AND article_id = ?');
    $bk->execute([user()['id'], $id]);
    $isBookmarked = (bool)$bk->fetchColumn();
}

// Load visible comments
$comments = $pdo->prepare("SELECT m.*, u.name AS commenter, u.profile_photo AS commenter_photo
                           FROM comments m
                           JOIN users u ON u.id = m.user_id
                           WHERE m.article_id = ? AND m.status = 'visible'
                           ORDER BY m.created_at DESC");
$comments->execute([$id]);
$commentRows = $comments->fetchAll();

$pageTitle = $article['title'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <!-- Article badges -->
    <div class="d-flex gap-2 mb-3 flex-wrap">
      <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold"><?= e($article['cat']) ?></span>
      <?php if ($article['is_breaking']): ?><span class="badge bg-danger text-white shadow-sm"><i class="bi bi-lightning-fill me-1"></i>Breaking</span><?php endif; ?>
      <?php if ($article['is_featured']): ?><span class="badge bg-primary text-white shadow-sm"><i class="bi bi-star-fill me-1"></i>Featured</span><?php endif; ?>
    </div>

    <h1 class="fw-bold mb-3" style="color: var(--nsbm-navy);"><?= e($article['title']) ?></h1>

    <!-- Author & metadata -->
    <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
      <img src="<?= user_avatar_url(['profile_photo' => $article['author_photo']]) ?>" width="44" height="44" class="rounded-circle border border-2 shadow-sm" style="object-fit: cover;" alt="">
      <div>
        <div class="fw-semibold"><?= e($article['author_name']) ?></div>
        <div class="text-muted small">
          <i class="bi bi-calendar3 me-1"></i><?= fmt_date($article['created_at']) ?>
          &bull; <i class="bi bi-eye me-1"></i><?= number_format((int)$article['views'] + 1) ?> views
          &bull; <i class="bi bi-chat me-1"></i><?= count($commentRows) ?> comments
        </div>
      </div>
      <?php if (is_logged_in()): ?>
      <form method="post" class="ms-auto">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="bookmark">
        <button class="btn btn-sm <?= $isBookmarked ? 'btn-green' : 'btn-outline-success' ?>">
          <i class="bi <?= $isBookmarked ? 'bi-bookmark-fill' : 'bi-bookmark' ?> me-1"></i>
          <?= $isBookmarked ? 'Saved' : 'Save' ?>
        </button>
      </form>
      <?php endif; ?>
    </div>

    <!-- Article image -->
    <?php if (!empty($article['image'])): ?>
      <img src="<?= img_url($article['image']) ?>" class="img-fluid rounded-4 mb-4 w-100 shadow-sm" style="max-height: 440px; object-fit: cover;" alt="<?= e($article['title']) ?>">
    <?php endif; ?>

    <!-- Article body -->
    <div class="article-body lh-lg fs-5 mb-5">
      <?= nl2br(e($article['body'])) ?>
    </div>

    <!-- Share / back -->
    <div class="d-flex justify-content-between align-items-center mb-5 pb-3 border-bottom">
      <a href="news.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to News</a>
      <a href="news.php?cat=<?= (int)$article['category_id'] ?>" class="btn btn-outline-success btn-sm">
        More in <?= e($article['cat']) ?>
      </a>
    </div>

    <!-- Comments section -->
    <h4 class="fw-bold mb-3" style="color: var(--nsbm-navy);"><i class="bi bi-chat-dots me-2"></i>Comments (<?= count($commentRows) ?>)</h4>

    <?php if (is_logged_in()): ?>
    <form method="post" class="box mb-4 shadow-sm">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="comment">
      <div class="mb-3">
        <label class="form-label fw-semibold" for="comment_body">Leave a comment</label>
        <textarea class="form-control" id="comment_body" name="comment_body" rows="3" required minlength="3" maxlength="1000" placeholder="Share your thoughts..."></textarea>
      </div>
      <button class="btn btn-green btn-sm"><i class="bi bi-send me-1"></i>Post Comment</button>
    </form>
    <?php else: ?>
    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle me-1"></i><a href="login.php" class="fw-semibold">Log in</a> to leave a comment.
    </div>
    <?php endif; ?>

    <?php foreach ($commentRows as $c): ?>
    <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
      <img src="<?= user_avatar_url(['profile_photo' => $c['commenter_photo']]) ?>" width="40" height="40" class="rounded-circle border shadow-sm flex-shrink-0" style="object-fit: cover;" alt="">
      <div>
        <div class="fw-semibold"><?= e($c['commenter']) ?> <span class="text-muted small fw-normal ms-2"><?= fmt_date($c['created_at']) ?></span></div>
        <p class="mb-0 mt-1"><?= nl2br(e($c['body'])) ?></p>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if (!$commentRows): ?>
    <p class="text-muted">No comments yet. Be the first to share your thoughts!</p>
    <?php endif; ?>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
