<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login | NSBM NewsDesk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 auth-page">

<?php
  include 'navbar.php'
?>

<main class="container py-4 flex-grow-1">
<div class="box login-box">
  <h2>Admin Login</h2>
  <p class="text-muted">Only NewsDesk admins can login here.</p>
  <form>
    <div class="mb-3">
      <label class="form-label" for="email">Admin email</label>
      <input class="form-control" id="email" name="email" type="email" required>
    </div>
    <div class="mb-3">
      <label class="form-label" for="password">Password</label>
      <input class="form-control" id="password" name="password" type="password" required>
    </div>
    <a class="btn btn-green w-100" href="index.php">Login</a>
  </form>
  <p class="mt-3 mb-0 text-center"><a href="adminlogin.html">Student login</a></p>
</div>
</main>

<?php
include 'footer'
?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
