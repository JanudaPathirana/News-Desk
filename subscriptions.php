<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$uid = user()['id'];
$cats = all_categories();

// save the selected categories
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    csrf_check();
    $chosen = array_map('intval', $_POST['cats'] ?? []);
    $valid = array_map('intval', array_column($cats, 'id'));
    $chosen = array_intersect($chosen, $valid);

    // delete old ones and insert the new ones
    $pdo->prepare('DELETE FROM subscriptions WHERE user_id = ?')->execute([$uid]);
    $ins = $pdo->prepare('INSERT INTO subscriptions (user_id, category_id) VALUES (?, ?)');
    foreach ($chosen as $cid) {
        $ins->execute([$uid, $cid]);
    }

    flash('success', 'Subscriptions saved.');
    redirect('subscriptions.php');
}

// categories this user already follows
$st = $pdo->prepare('SELECT category_id FROM subscriptions WHERE user_id = ?');
$st->execute([$uid]);
$mine = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));

$pageTitle = 'Subscriptions';
$active = 'subs';
require_once __DIR__ . '/includes/header.php';
?>
<h1>Category Subscriptions</h1>
<p class="text-muted">Select the categories you like. Their news will show first on your home page.</p>

<form method="post" class="box">
  <?= csrf_field() ?>
  <?php foreach ($cats as $c): ?>
    <div class="form-check mb-2">
      <input class="form-check-input" type="checkbox" name="cats[]" id="c<?= (int)$c['id'] ?>" value="<?= (int)$c['id'] ?>" <?= in_array((int)$c['id'], $mine) ? 'checked' : '' ?>>
      <label class="form-check-label" for="c<?= (int)$c['id'] ?>"><?= e($c['name']) ?></label>
    </div>
  <?php endforeach; ?>
  <button class="btn btn-green mt-2">Save Subscriptions</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
