<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$uid = user()['id'];

// remove a saved article
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    if (post('action') == 'remove') {
        $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?')->execute([$uid, (int)post('id')]);
        flash('success', 'Removed from saved articles.');
    }
    redirect('bookmarks.php');
}

$st = $pdo->prepare("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                     FROM bookmarks b
                     JOIN articles a ON a.id = b.article_id AND a.status = 'approved'
                     JOIN categories c ON c.id = a.category_id
                     WHERE b.user_id = ? ORDER BY b.created_at DESC");
$st->execute([$uid]);
$rows = $st->fetchAll();

$pageTitle = 'Saved Articles';
$active = 'bookmarks';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/card.php';
?>
<h1 class="mb-3">Saved Articles</h1>

<div class="row g-4">
  <?php
  foreach ($rows as $a) {
      // remove button under each card
      $extra = '<form method="post" class="mt-2">' . csrf_field()
             . '<input type="hidden" name="action" value="remove">'
             . '<input type="hidden" name="id" value="' . (int)$a['id'] . '">'
             . '<button class="btn btn-sm btn-outline-danger">Remove</button></form>';
      news_card($a, $base, $extra);
  }
  ?>
</div>
<?php if (!$rows): ?>
  <div class="box text-center">You have not saved any articles yet. <a href="news.php">Browse the news</a></div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
