<?php
if (!isset($pageTitle)) { $pageTitle = "Student Expense Splitter"; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($pageTitle) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">💰 Expense Splitter</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbars" aria-controls="navbars" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbars">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#how">How it works</a></li>
        
      </ul>
    </div>
  </div>
</nav>
<header class="hero py-5">
  <div class="container text-center">
    <h1 class="display-5 fw-bold text-white" data-aos="fade-up">Split Bills Smartly, Stay Friends ❤️</h1>
    <p class="lead text-white-50" data-aos="fade-up" data-aos-delay="150">Create a group, add expenses, and let the app calculate who pays whom.</p>
  </div>
</header>
<div class="container my-4">
