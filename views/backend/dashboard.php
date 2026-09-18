<?php
require_once dirname(__DIR__,2).'/config.php';
require_admin();
require ROOT.'/header.php';
?>
<main class="container py-4"><h1>Administration de la médiathèque</h1>
<?php flashes(); ?>
<p>Créez les groupes et artistes, puis les albums et leurs titres.</p>
<div class="list-group">
<?php foreach (resources() as $entity=>$resource): ?>
<a class="list-group-item list-group-item-action" href="<?= h(BASE_URL.'/views/backend/'.$entity.'/list.php') ?>"><?= h($resource['label']) ?></a>
<?php endforeach; ?></div></main>
<?php require ROOT.'/footer.php'; ?>
