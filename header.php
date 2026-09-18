<?php
require_once __DIR__ . '/config.php';

$assetUrl = static function (string $path): string {
    return htmlspecialchars(BASE_URL . '/' . ltrim($path, '/'), ENT_QUOTES, 'UTF-8');
};
$pagePath = parse_url($_SERVER['REQUEST_URI'] ?? '/index.php', PHP_URL_PATH);
$pageTitles = ['login.php'=>'Connexion','signup.php'=>'Créer un compte','rgpd.php'=>'Confidentialité et RGPD','cgu.php'=>'Conditions d’utilisation','dashboard.php'=>'Administration'];
$pageTitle = $pageTitle ?? (($pageTitles[basename($pagePath)] ?? 'Albums, artistes et découvertes musicales').' — Médiathèque');
$pageDescription = $pageDescription ?? 'Explorez notre collection musicale, découvrez les artistes et les pistes de chaque album et gardez vos coups de cœur en favoris.';
$pageImage = $pageImage ?? BASE_URL.'/src/images/Michel.png';
$basePath = rtrim(parse_url(BASE_URL, PHP_URL_PATH) ?: '', '/');
$canonicalPath = $basePath !== '' && str_starts_with($pagePath,$basePath.'/') ? substr($pagePath,strlen($basePath)) : $pagePath;
$canonicalPath = $canonicalPath === '/' ? '/index.php' : $canonicalPath;
if (basename($pagePath) === 'album.php' && isset($albumId)) $canonicalPath .= '?id='.(int)$albumId;
$canonical = BASE_URL.$canonicalPath;
$noindex = str_contains($pagePath,'/backend/') || isset($_GET['q']) || http_response_code() === 404;
?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription) ?>">
    <meta name="robots" content="<?= $noindex?'noindex, follow':'index, follow' ?>">
    <link rel="canonical" href="<?= h($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:site_name" content="Médiathèque">
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription) ?>">
    <meta property="og:url" content="<?= h($canonical) ?>">
    <meta property="og:image" content="<?= h($pageImage) ?>">
    <meta property="og:image:alt" content="<?= h(isset($album)?'Pochette de '.$album['nomA']:'Michel, mascotte de la médiathèque') ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= h($pageTitle) ?>">
    <meta name="twitter:description" content="<?= h($pageDescription) ?>">
    <meta name="twitter:image" content="<?= h($pageImage) ?>">
    <meta name="twitter:image:alt" content="<?= h(isset($album)?'Pochette de '.$album['nomA']:'Michel, mascotte de la médiathèque') ?>">
    <!-- Load CSS -->

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?= $assetUrl('src/css/style.css') ?>" />
    <link rel="shortcut icon" type="image/png" href="<?= $assetUrl('src/images/Michel.png') ?>" />
</head>
<body data-image-fallback="<?= $assetUrl('src/images/Michel.png') ?>" data-base-url="<?= h(BASE_URL) ?>">
<?php if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php'): ?>
<div class="site-intro-layer is-pending" id="siteIntroLayer" role="dialog" aria-modal="true" aria-labelledby="site-intro-title">
  <div class="site-intro-pattern" aria-hidden="true"></div>
  <div class="site-intro-content">
    <img class="site-intro-michel" src="<?= $assetUrl('src/images/Michel.png') ?>" alt="Michel, mascotte de la médiathèque">
    <p class="site-intro-kicker">Médiathèque MMI Bordeaux</p>
    <h1 id="site-intro-title">Les productions MMI</h1>
    <button class="site-intro-button" id="siteIntroButton" type="button">Découvrir les productions MMI</button>
  </div>
</div>
<?php endif; ?>
<?php if (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'album.php'): ?><a class="skip-content" href="#contenu">Aller au contenu</a><?php endif; ?>
<nav class="navbar navbar-expand-lg site-header">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= $assetUrl('index.php') ?>"><img class="brand-michel" src="<?= $assetUrl('src/images/Michel.png') ?>" alt="Michel, mascotte de la médiathèque"> Médiathèque</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Ouvrir le menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="<?= $assetUrl('index.php') ?>">Catalogue</a>
        </li>
        <?php if (check_access()): ?><li class="nav-item">
          <a class="nav-link" href="<?= $assetUrl('views/backend/dashboard.php') ?>">Admin</a>
        </li><?php endif; ?>
      </ul>
    </div>
    <!-- right align -->
    <div class="d-flex">
      <form class="d-flex" role="search" method="get" action="<?= $assetUrl('index.php') ?>">
          <input class="form-control me-2" type="search" name="q" placeholder="Rechercher sur le site…" aria-label="Rechercher un album ou un artiste">
      </form>
      <?php if (is_logged()): ?>
      <form method="post" action="<?= $assetUrl('api/security/disconnect.php') ?>">
        <input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
        <button class="btn btn-dark m-1" type="submit">Déconnexion</button>
      </form>
      <?php else: ?>
      <a class="nav-link account-link" href="<?= $assetUrl('views/backend/security/login.php') ?>">Connexion</a>
      <a class="nav-link account-link" href="<?= $assetUrl('views/backend/security/signup.php') ?>">Inscription</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
