<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Médiathèque</title>
    <!-- Load CSS -->
    <link rel="stylesheet" href="/src/css/style.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="shortcut icon" type="image/x-icon" href="src/images/article1.png" />
    <!-- Google Fonts & styles spécifiques -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Médiathèque</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/views/backend/dashboard.php">Dashboard</a>
        </li>
      </ul>
    </div>
    <!-- right align -->
    <div class="d-flex">
      <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Rechercher sur le site…" aria-label="Search">
      </form>
      <?php if (is_logged()): ?>
        <a class="btn btn-primary m-1" href="/views/backend/dashboard.php" role="button">Dashboard</a>
        <form method="post" action="/views/backend/security/logout.php">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
          <button class="btn btn-dark m-1" type="submit">Déconnexion</button>
        </form>
      <?php else: ?>
        <a class="btn btn-primary m-1" href="/views/backend/security/login.php" role="button">Connexion</a>
        <a class="btn btn-dark m-1" href="/views/backend/security/signup.php" role="button">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
