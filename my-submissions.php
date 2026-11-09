<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$uid = user()['id'];

$st = $pdo->prepare('SELECT a.id, a.title, a.status, a.created_at, a.views, c.name AS cat
                     FROM articles a JOIN categories c ON c.id = a.category_id
                     WHERE a.author_id = ? ORDER BY a.created_at DESC');
$st->execute([$uid]);
$rows = $st->fetchAll();

$pageTitle = 'My Submissions';
$active = 'submit';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">My Submissions</h2>
    <a href="submit.php" class="btn btn-green"><i class="bi bi-plus-lg me-1"></i>New Article</a>
  </div>
  <div class="box p-0 overflow-hidden shadow-sm">
    <table class="table mb-0">
      <thead class="table-light">
        <tr><th>Title</th><th>Category</th><th>Status</th><th>Submitted</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= e($r['title']) ?></td>
            <td><?= e($r['cat']) ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td><?= fmt_date($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
