<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $article_id = (int)post('article_id');
    $user_id = user()['id'];

    $check = $pdo->prepare('SELECT 1 FROM bookmarks WHERE user_id = ? AND article_id = ?');
    $check->execute([$user_id, $article_id]);

    if ($check->fetch()) {
        $del = $pdo->prepare('DELETE FROM bookmarks WHERE user_id = ? AND article_id = ?');
        $del->execute([$user_id, $article_id]);
        flash('info', 'Bookmark removed.');
    } else {
        $ins = $pdo->prepare('INSERT INTO bookmarks (user_id, article_id) VALUES (?, ?)');
        $ins->execute([$user_id, $article_id]);
        flash('success', 'Article saved to your bookmarks.');
    }
}
redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
