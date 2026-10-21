<html>
<head>
  <title>Home | NSBM NewsDesk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

  <?php
  include 'navbar.php';
  ?>

<main class="container py-4 flex-grow-1">
<section class="campus-banner">
  <p class="banner-small">NSBM Green University</p>
  <h1>What's happening on campus?</h1>
  <p>News, events and notices from your faculty and clubs, all in one place.</p>
  <form class="d-flex gap-2 mb-3" action="news.html" method="get" style="max-width:480px">
    <input class="form-control rounded-pill" type="text" name="q" placeholder="Search news...">
    <button class="btn btn-light-green">Search</button>
  </form>
  <a class="btn btn-outline-light btn-sm" href="submit.html">Submit an article</a>
</section>

<div class="box">
  <div class="row">
    <div class="col-md-5">
      <img class="hero-img" src="images/placeholder.svg" alt="main news">
    </div>
    <div class="col-md-7">
      <span class="badge bg-warning text-dark">Breaking</span>
      <span class="badge bg-success">Featured</span>
      <span class="badge bg-secondary">Sports</span>
      <h2 class="mt-2">Annual Sports Meet 2026 Announced</h2>
      <p>The annual inter-faculty sports meet will be held next month at the main ground. All students can register their teams.</p>
      <p class="text-muted">12 Oct 2026 | 120 views</p>
      <a class="btn btn-green" href="article.html">Read full story</a>
    </div>
  </div>
</div>

<h3 class="section-title">Announcements</h3>
<div class="alert alert-warning">
  <strong>Exam notice</strong><br>
  Final exams start on 3rd November. Check your timetable. <small class="text-muted">(11 Oct 2026)</small>
</div>
<div class="alert alert-warning">
  <strong>Library closed on Poya day</strong><br>
  The library will be closed this Saturday. <small class="text-muted">(09 Oct 2026)</small>
</div>

<h3 class="section-title">Latest News</h3>
<div class="row g-4">
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
<a href="news.html" class="btn btn-outline-dark mt-3">View all news</a>

<h3 class="section-title">Browse by Category</h3>
<a class="btn btn-outline-success btn-sm mb-1" href="news.html">Academic (3)</a>
<a class="btn btn-outline-success btn-sm mb-1" href="news.html">Campus (2)</a>
<a class="btn btn-outline-success btn-sm mb-1" href="news.html">Clubs (4)</a>
<a class="btn btn-outline-success btn-sm mb-1" href="news.html">Events (3)</a>
<a class="btn btn-outline-success btn-sm mb-1" href="news.html">Sports (2)</a>

<div class="box mt-5 d-flex justify-content-between align-items-center flex-wrap">
  <div>
    <h5>Only want some topics?</h5>
    <span class="text-muted">Subscribe to categories and see their news first.</span>
  </div>
  <a class="btn btn-light-green" href="subscriptions.html">Manage Subscriptions</a>
</div>
</main>

<footer class="footer no-print">
  <div class="container text-center">
    NSBM NewsDesk - Campus News Management System | NSBM Green University &copy; 2026
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
