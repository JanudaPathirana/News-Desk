<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Article | NSBM NewsDesk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php
'include navbar.php'
?>

<main class="container py-4 flex-grow-1">
<a href="news.html">&laquo; Back to all news</a>

<div class="mt-3">
  <span class="badge bg-success">Sports</span>
  <span class="badge bg-warning text-dark">Breaking</span>
  <span class="badge bg-primary">Featured</span>

  <h1 class="mt-2">Annual Sports Meet 2026 Announced</h1>
  <p class="text-muted">By Nimali Silva | 12 Oct 2026 | 120 views</p>

  <img class="cover-img mb-4" src="images/placeholder.svg" alt="article image">

  <div class="fs-5">
    The annual inter-faculty sports meet will be held next month at the main ground. All students can register their teams through the sports club.<br><br>
    Events include athletics, cricket, netball, football and a relay for all faculties. Students who want to take part should meet their faculty captain before the end of this week.<br><br>
    Free water and first aid will be available at the ground the whole day.
  </div>
</div>

<div class="d-flex gap-2 my-4 no-print">
  <button class="btn btn-outline-dark">Save for later</button>
  <button class="btn btn-outline-dark">Subscribe to Sports</button>
  <button class="btn btn-outline-dark" type="button" onclick="window.print()">Print</button>
</div>

<h4 class="section-title" id="comments">Comments (2)</h4>

<form class="mb-3">
  <label class="form-label" for="ctext">Add a comment</label>
  <textarea class="form-control" id="ctext" name="ctext" rows="3"></textarea>
  <button type="button" class="btn btn-green mt-2">Post Comment</button>
</form>

<div class="box mb-2">
  <div class="d-flex justify-content-between">
    <strong>Kasun Perera</strong>
    <small class="text-muted">12 Oct 2026</small>
  </div>
  <div class="mt-1">Great news! Cannot wait for the relay.</div>
  <button class="btn btn-sm btn-outline-danger mt-2">Delete</button>
</div>
<div class="box mb-2">
  <div class="d-flex justify-content-between">
    <strong>Nimali Silva</strong>
    <small class="text-muted">12 Oct 2026</small>
  </div>
  <div class="mt-1">When is the last date to register?</div>
</div>
</main>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
