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