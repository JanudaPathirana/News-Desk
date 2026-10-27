<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>All News | NSBM NewsDesk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<?php
include 'navbar.php';
?>

<main class="container py-4 flex-grow-1">
<h1 class="mb-3">All News</h1>

<form class="box mb-4 no-print">
  <div class="row g-3 align-items-end">
    <div class="col-md-5">
      <label class="form-label" for="q">Search</label>
      <input class="form-control" id="q" name="q" placeholder="Search news">
    </div>
    <div class="col-md-3">
      <label class="form-label" for="cat">Category</label>
      <select class="form-select" id="cat" name="cat">
        <option value="0">All categories</option>
        <option>Academic</option>
        <option>Campus</option>
        <option>Clubs</option>
        <option>Events</option>
        <option>Sports</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label" for="from">Published after</label>
      <input type="date" class="form-control" id="from" name="from">
    </div>
    <div class="col-md-2">
      <button type="button" class="btn btn-green">Filter</button>
      <a class="btn btn-outline-dark" href="news.html">Clear</a>
    </div>
  </div>
</form>

<p class="text-muted">6 article(s) found</p>
<div class="row g-4">
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Sports</span>
        <span class="badge bg-warning text-dark">Breaking</span>
        <h5 class="card-title mt-2">
          <a href="article.html">Annual Sports Meet 2026 Announced</a>
        </h5>
        <p class="card-text text-muted">The annual inter-faculty sports meet will be held next month at the main ground. All students can register the...</p>
        <small class="text-muted">12 Oct 2026 | 120 views</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Clubs</span>
        <h5 class="card-title mt-2">
          <a href="article.html">Computing Faculty Hackathon Winners</a>
        </h5>
        <p class="card-text text-muted">Team CodeRunners won first place in this year's hackathon with their campus bus tracking app....</p>
        <small class="text-muted">10 Oct 2026 | 98 views</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Campus</span>
        <h5 class="card-title mt-2">
          <a href="article.html">New Library Opening Hours</a>
        </h5>
        <p class="card-text text-muted">The library will stay open until 10 PM during the exam season. Group study rooms can be booked online....</p>
        <small class="text-muted">08 Oct 2026 | 75 views</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Events</span>
        <h5 class="card-title mt-2">
          <a href="article.html">Career Fair Next Week</a>
        </h5>
        <p class="card-text text-muted">More than 30 companies will visit the university for the career fair. Bring your CV and student ID....</p>
        <small class="text-muted">05 Oct 2026 | 64 views</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Clubs</span>
        <h5 class="card-title mt-2">
          <a href="article.html">Photography Club Exhibition</a>
        </h5>
        <p class="card-text text-muted">The photography club is showing the best photos from this year in the main hall until Friday....</p>
        <small class="text-muted">03 Oct 2026 | 41 views</small>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100">
      <a href="article.html">
        <img class="card-img-top news-img" src="images/placeholder.svg" alt="news image">
      </a>
      <div class="card-body">
        <span class="badge bg-success">Academic</span>
        <h5 class="card-title mt-2">
          <a href="article.html">Semester Exam Timetable Released</a>
        </h5>
        <p class="card-text text-muted">The final exam timetable for all batches is now available. Check your faculty notice board for details....</p>
        <small class="text-muted">01 Oct 2026 | 150 views</small>
      </div>
    </div>
  </div>
</div>

<nav class="mt-4 no-print">
  <ul class="pagination justify-content-center">
    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
    <li class="page-item active"><a class="page-link" href="#">1</a></li>
    <li class="page-item"><a class="page-link" href="#">2</a></li>
    <li class="page-item"><a class="page-link" href="#">Next</a></li>
  </ul>
</nav>
</main>

<?php
include 'footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
