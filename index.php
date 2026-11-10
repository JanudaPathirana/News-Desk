<?php
require_once __DIR__ . '/includes/auth.php';

// Require login — redirect unauthenticated users to the login page
require_login();

require_once __DIR__ . '/includes/card.php';

$st = $pdo->query("SELECT a.id, a.title, a.body, a.image, a.created_at, a.views, a.is_breaking, c.name AS cat
                   FROM articles a
                   JOIN categories c ON c.id = a.category_id
                   WHERE a.status = 'approved'
                   ORDER BY a.created_at DESC LIMIT 6");
$latest = $st->fetchAll();

$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-4">
  <div class="campus-banner text-center mb-5">
    <h1 class="display-5 fw-bold text-white mb-2">NSBM NewsDesk</h1>
    <p class="text-white-50 lead">The official campus news and media hub of NSBM Green University</p>
  </div>
  <div class="row g-4">
    <div class="col-12">
      <h3 class="fw-bold mb-3">Latest Campus News</h3>
    </div>
    <?php foreach ($latest as $a): ?>
      <?php news_card($a); ?>
    <?php endforeach; ?>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
