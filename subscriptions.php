<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$uid = user()['id'];
$cats = all_categories();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $chosen = array_map('intval', $_POST['cats'] ?? []);
    $pdo->prepare('DELETE FROM subscriptions WHERE user_id = ?')->execute([$uid]);
    $ins = $pdo->prepare('INSERT INTO subscriptions (user_id, category_id) VALUES (?, ?)');
    foreach ($chosen as $cid) {
        $ins->execute([$uid, $cid]);
    }
    flash('success', 'Subscriptions saved.');
    redirect('subscriptions.php');
}

$st = $pdo->prepare('SELECT category_id FROM subscriptions WHERE user_id = ?');
$st->execute([$uid]);
$mine = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));

$pageTitle = 'Subscriptions';
$active = 'subs';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="container my-5">
  <div class="box p-4 shadow-sm">
    <h3 class="fw-bold mb-2">Category Subscriptions</h3>
    <form method="post">
      <div class="mb-3">
        <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-secondary">Select All Categories</button>
      </div>
      <?= csrf_field() ?>
      <?php foreach ($cats as $c): ?>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" name="cats[]" value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $mine) ? 'checked' : '' ?>>
          <label class="form-check-label"><?= e($c['name']) ?></label>
        </div>
      <?php endforeach; ?>
      <button class="btn btn-green mt-3">Save</button>
    </form>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
