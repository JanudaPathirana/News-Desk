<?php
$base = $base ?? '';
$active = $active ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-green shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= $base ?>index.php">
      <i class="bi bi-newspaper"></i>
      <span>NSBM</span> NewsDesk
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $active == 'home' ? 'active' : '' ?>" href="<?= $base ?>index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $active == 'news' ? 'active' : '' ?>" href="<?= $base ?>news.php">All News</a>
        </li>
      </ul>
      <div class="d-flex align-items-center gap-2">
        <a class="btn btn-outline-light btn-sm" href="<?= $base ?>login.php">Login</a>
        <a class="btn btn-light-green btn-sm" href="<?= $base ?>register.php">Register</a>
      </div>
    </div>
  </div>
</nav>
