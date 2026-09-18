<?php
require_once dirname(__DIR__,2).'/config.php';
require_admin();
require ROOT.'/header.php';
?>
<main class="container py-5 admin-panel"><p class="eyebrow dark">Votre espace de gestion</p><h1>Administration</h1>
<?php flashes(); ?><p>La collection, ses artistes et ses membres, au même endroit.</p>
<div class="admin-grid">
<?php foreach (resources() as $entity=>$resource): ?>
<article class="admin-block"><span class="admin-count"><?= (int)db()->query('SELECT COUNT(*) FROM `'.$resource['table'].'`')->fetchColumn() ?> éléments</span>
<h2><?= h($resource['label']) ?></h2><p>Consultez, modifiez ou supprimez les éléments de cette liste.</p>
<div class="admin-actions"><a class="btn btn-primary" href="<?= h(BASE_URL.'/views/backend/'.$entity.'/list.php') ?>">Voir et gérer</a><a class="btn btn-outline-dark" href="<?= h(BASE_URL.'/views/backend/'.$entity.'/create.php') ?>">Créer</a></div>
</article><?php endforeach; ?></div></main>
<?php require ROOT.'/footer.php'; ?>
