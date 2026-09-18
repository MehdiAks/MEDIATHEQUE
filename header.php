<?php
require_once __DIR__ . '/config.php';

$assetUrl = static function (string $path): string {
    return htmlspecialchars(BASE_URL . '/' . ltrim($path, '/'), ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Médiathèque</title>
    <!-- Load CSS -->
    <link rel="stylesheet" href="<?= $assetUrl('src/css/style.css') ?>" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="shortcut icon" type="image/png" href="<?= $assetUrl('src/images/article.png') ?>" />
    <!-- Google Fonts & styles spécifiques -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $assetUrl('index.php') ?>">Médiathèque</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="<?= $assetUrl('index.php') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= $assetUrl('views/backend/dashboard.php') ?>">Admin</a>
        </li>
      </ul>
    </div>
    <!-- right align -->
    <div class="d-flex">
      <form class="d-flex" role="search" method="get" action="<?= $assetUrl('index.php') ?>">
          <input class="form-control me-2" type="search" name="q" placeholder="Rechercher sur le site…" aria-label="Search">
      </form>
      <?php if (is_logged()): ?>
      <form method="post" action="<?= $assetUrl('api/security/disconnect.php') ?>">
        <input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
        <button class="btn btn-dark m-1" type="submit">Déconnexion</button>
      </form>
      <?php else: ?>
      <a class="btn btn-primary m-1" href="<?= $assetUrl('views/backend/security/login.php') ?>">Connexion</a>
      <a class="btn btn-dark m-1" href="<?= $assetUrl('views/backend/security/signup.php') ?>">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
