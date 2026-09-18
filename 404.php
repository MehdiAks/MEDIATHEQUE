<?php
require_once __DIR__.'/config.php';
http_response_code(404);
$pageTitle='Page introuvable — Médiathèque';
require ROOT.'/header.php';
?>
<main class="auth-page"><section class="auth-card error-page"><img class="auth-michel" src="<?= h(BASE_URL.'/src/images/Michel.png') ?>" alt="Michel, mascotte de la médiathèque"><p class="eyebrow dark">Erreur 404</p><h1>Cette page est introuvable</h1><p>Le lien a peut-être changé ou l’album a été supprimé. Retrouvez la collection depuis le catalogue.</p><a class="btn btn-primary" href="<?= h(BASE_URL.'/index.php') ?>">Découvrir les albums</a></section></main>
<?php require ROOT.'/footer.php'; ?>
