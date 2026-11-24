<?php
require_once __DIR__ . '/includes/auth.php';

// get the article
$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare("SELECT a.*, c.name AS cat, u.name AS author
                     FROM articles a
                     JOIN categories c ON c.id = a.category_id
                     JOIN users u ON u.id = a.author_id
                     WHERE a.id = ?");
$st->execute([$id]);
$art = $st->fetch();

// only approved articles are public, admin and the writer can still see theirs
$canSee = false;
if ($art) {
    if ($art['status'] == 'approved' || is_admin()) {
        $canSee = true;
    } elseif (is_logged_in() && $art['author_id'] == user()['id']) {
        $canSee = true;
    }
}
if (!$canSee) {
    flash('danger', 'Article not found.');
    redirect('news.php');
}

// ---------- forms on this page (comment, delete comment, bookmark, subscribe) ----------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    require_login();
    $action = post('action');
    $uid = user()['id'];

    if ($action == 'comment') {
        $text = post('ctext');
        if (mb_strlen($text) < 2 || mb_strlen($text) > 1000) {
            flash('danger', 'Comment must be between 2 and 1000 characters.');
        } elseif ($art['status'] != 'approved') {
            flash('danger', 'You can only comment on published articles.');
        } else {
            $pdo->prepare('INSERT INTO comments (article_id, user_id, body) VALUES (?, ?, ?)')->execute([$id, $uid, $text]);
            flash('success', 'Comment added.');
        }
    } elseif ($action == 'del_comment') {
        // students can delete only their own comments, admin can delete any
        $cid = (int)post('comment_id');
        $pdo->prepare('DELETE FROM comments WHERE id = ? AND article_id = ? AND (user_id = ? OR ? = 1)')
            ->execute([$cid, $id, $uid, is_admin() ? 1 : 0]);
        flash('success', 'Comment deleted.');
    } elseif ($action == 'bookmark' && $art['status'] == 'approved') {
        // if already saved remove it, else save it
        $chk = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id = ? AND article_id = ?');
        $chk->execute([$uid, $id]);
        if ($chk->fetchColumn()) {
            $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?')->execute([$uid, $id]);
            flash('success', 'Removed from saved articles.');
        } else {
            $pdo->prepare('INSERT INTO bookmarks (user_id, article_id) VALUES (?, ?)')->execute([$uid, $id]);
            flash('success', 'Article saved.');
        }
    } elseif ($action == 'subscribe' && $art['status'] == 'approved') {
        $chk = $pdo->prepare('SELECT 1 FROM subscriptions WHERE user_id = ? AND category_id = ?');
        $chk->execute([$uid, $art['category_id']]);
        if ($chk->fetchColumn()) {
            $pdo->prepare('DELETE FROM subscriptions WHERE user_id = ? AND category_id = ?')->execute([$uid, $art['category_id']]);
            flash('success', 'Unsubscribed from ' . $art['cat'] . '.');
        } else {
            $pdo->prepare('INSERT INTO subscriptions (user_id, category_id) VALUES (?, ?)')->execute([$uid, $art['category_id']]);
            flash('success', 'Subscribed to ' . $art['cat'] . '.');
        }
    }
    redirect('article.php?id=' . $id . '#comments');
}

// count the view only one time per session
if ($art['status'] == 'approved' && empty($_SESSION['viewed'][$id])) {
    $pdo->prepare('UPDATE articles SET views = views + 1 WHERE id = ?')->execute([$id]);
    $_SESSION['viewed'][$id] = true;
    $art['views']++;
}

// check if the user saved / subscribed
$bookmarked = false;
$subscribed = false;
if (is_logged_in()) {
    $q = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id = ? AND article_id = ?');
    $q->execute([user()['id'], $id]);
    $bookmarked = (bool)$q->fetchColumn();

    $q = $pdo->prepare('SELECT 1 FROM subscriptions WHERE user_id = ? AND category_id = ?');
    $q->execute([user()['id'], $art['category_id']]);
    $subscribed = (bool)$q->fetchColumn();
}

// comments
$cm = $pdo->prepare("SELECT m.*, u.name, u.profile_photo, u.role FROM comments m
                     JOIN users u ON u.id = m.user_id
                     WHERE m.article_id = ? AND m.status = 'visible' ORDER BY m.created_at DESC");
$cm->execute([$id]);
$comments = $cm->fetchAll();

// Page title and navigation setup
$pageTitle = $art['title'];
$active = 'news';

// Include header and navigation bar
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<a href="news.php">&laquo; Back to all news</a>

<div class="mt-3">
  <?php if ($art['status'] === 'rejected'): ?>
    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-x-circle-fill fs-5"></i>
      <div class="flex-grow-1"><strong>Article Rejected!</strong> This article was rejected by the Admin. You can <a href="submit.php?id=<?= (int)$art['id'] ?>" class="alert-link">Edit and resubmit</a> it.</div>
    </div>
  <?php elseif ($art['status'] === 'pending'): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-hourglass-split fs-5"></i>
      <div class="flex-grow-1"><strong>Pending Approval:</strong> This article is waiting for Admin approval before it becomes public.</div>
    </div>
  <?php elseif (is_logged_in() && $art['author_id'] == user()['id']): ?>
    <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
      <i class="bi bi-check-circle-fill fs-5"></i>
      <div class="flex-grow-1"><strong>Article Approved!</strong> Your article has been approved by the Admin and is now published live.</div>
    </div>
  <?php endif; ?>

  <span class="badge bg-success"><?= e($art['cat']) ?></span>
  <?php if ($art['is_breaking']): ?><span class="badge bg-warning text-dark">Breaking</span><?php endif; ?>
  <?php if ($art['is_featured']): ?><span class="badge bg-primary">Featured</span><?php endif; ?>

  <h1 class="mt-2"><?= e($art['title']) ?></h1>
  <p class="text-muted">By <?= e($art['author']) ?> | <?= fmt_date($art['created_at']) ?> | <?= (int)$art['views'] ?> views</p>

  <?php if ($art['image']): ?>
    <img class="cover-img mb-4" src="<?= img_url($art['image'], $base) ?>" alt="article image">
  <?php endif; ?>

  <div class="fs-5"><?= nl2br(e($art['body'])) ?></div>
</div>

<div class="d-flex gap-2 my-4 no-print">
  <?php if ($art['status'] == 'approved'): ?>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="bookmark">
      <button class="btn <?= $bookmarked ? 'btn-green' : 'btn-outline-dark' ?>"><?= $bookmarked ? 'Saved' : 'Save for later' ?></button>
    </form>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="subscribe">
      <button class="btn <?= $subscribed ? 'btn-green' : 'btn-outline-dark' ?>"><?= $subscribed ? 'Subscribed to ' : 'Subscribe to ' ?><?= e($art['cat']) ?></button>
    </form>
  <?php endif; ?>
  <?php if (is_logged_in() && user()['id'] == $art['author_id']): ?>
    <a class="btn btn-outline-success" href="submit.php?id=<?= (int)$art['id'] ?>">
      <i class="bi bi-pencil me-1"></i> Edit Article
    </a>
    <form method="post" action="my-submissions.php" class="d-inline" onsubmit="return confirm('Delete this article completely?');">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
      <button class="btn btn-outline-danger">
        <i class="bi bi-trash me-1"></i> Delete Article
      </button>
    </form>
  <?php endif; ?>
  <button class="btn btn-outline-dark" type="button" onclick="window.print()">Print</button>
</div>

<h4 class="section-title" id="comments">Comments (<?= count($comments) ?>)</h4>

<?php if ($art['status'] == 'approved'): ?>
  <?php if (is_logged_in()): ?>
    <form method="post" class="mb-3">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="comment">
      <label class="form-label" for="ctext">Add a comment</label>
      <textarea class="form-control" id="ctext" name="ctext" rows="3" required minlength="2" maxlength="1000"></textarea>
      <button class="btn btn-green mt-2">Post Comment</button>
    </form>
  <?php else: ?>
    <p><a href="login.php">Log in</a> to write a comment.</p>
  <?php endif; ?>
<?php endif; ?>

<?php foreach ($comments as $c): ?>
  <div class="box mb-2">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <img src="<?= user_avatar_url($c, $base) ?>" width="32" height="32" class="rounded-circle border" style="object-fit: cover;" alt="<?= e($c['name']) ?>">
        <strong><?= e($c['name']) ?></strong>
        <?php if (!empty($c['role']) && $c['role'] === 'admin'): ?>
          <span class="badge bg-success" style="font-size: 10px;">Admin</span>
        <?php endif; ?>
      </div>
      <small class="text-muted"><?= fmt_date($c['created_at']) ?></small>
    </div>
    <div class="mt-2 ms-4 ps-2"><?= nl2br(e($c['body'])) ?></div>
    <?php if (is_logged_in() && ($c['user_id'] == user()['id'] || is_admin())): ?>
      <form method="post" class="mt-2" onsubmit="return confirm('Delete this comment?');">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="del_comment">
        <input type="hidden" name="comment_id" value="<?= (int)$c['id'] ?>">
        <button class="btn btn-sm btn-outline-danger">Delete</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
<?php if (!$comments): ?><p class="text-muted">No comments yet. Be the first one!</p><?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
