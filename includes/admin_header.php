<?php
// Admin page header and authentication check
require_once __DIR__ . '/auth.php';
require_admin();

$base = $base ?? '../';
$adminTitle = $adminTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($adminTitle) ?> | NewsDesk Admin</title>

  <!-- Prevent Empty Favicon 404 Error -->
  <link rel="icon" href="data:,">

  <!-- Google Fonts: Plus Jakarta Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <!-- Custom Stylesheet -->
  <link rel="stylesheet" href="<?= $base ?>assests/css/style.css">
</head>
<body class="bg-light">

<div class="container-fluid">
  <div class="row">
    <!-- Admin Sidebar -->
    <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse text-white min-vh-100 p-3">
      <div class="position-sticky">
        <h4 class="text-white py-3 px-2 border-bottom border-secondary">Admin Panel</h4>
        <ul class="nav flex-column gap-1 pt-2">
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/index.php">
              <i class="bi bi-speedometer2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/articles.php">
              <i class="bi bi-file-text"></i> Articles
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/pending.php">
              <i class="bi bi-hourglass-split"></i> Pending Approvals
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/categories.php">
              <i class="bi bi-tags"></i> Categories
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/announcements.php">
              <i class="bi bi-megaphone"></i> Announcements
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/comments.php">
              <i class="bi bi-chat-dots"></i> Comments
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center gap-2" href="/News-Desk/admin/reports.php">
              <i class="bi bi-graph-up"></i> Reports
            </a>
          </li>
          <hr class="border-secondary my-3">
          <li class="nav-item">
            <a class="nav-link text-danger d-flex align-items-center gap-2" href="/News-Desk/index.php">
              <i class="bi bi-box-arrow-left"></i> Exit to Site
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <!-- Main Content Area Wrapper Start -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
