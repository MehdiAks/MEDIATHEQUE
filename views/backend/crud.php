<?php
require_once dirname(__DIR__,2).'/config.php';
require_admin();
$resource = resources()[$entity];
$record = [];
try {
    if (in_array($page,['edit','delete'])) {
        [$where,$params]=resource_key($resource,$_GET);
        $record=query_rows('SELECT * FROM `'.$resource['table'].'` WHERE '.$where,$params)[0] ?? null;
        if (!$record) { http_response_code(404); exit('Élément introuvable.'); }
    }
} catch (InvalidArgumentException $e) { http_response_code(400); exit(h($e->getMessage())); }
$old=$_SESSION['old'][$entity] ?? []; unset($_SESSION['old'][$entity]);
require ROOT.'/header.php';
?>
<main class="container py-4">
<a href="<?= h(BASE_URL.'/views/backend/dashboard.php') ?>">Administration</a>
<h1><?= h($resource['label']) ?></h1>
<?php flashes(); ?>
<?php if ($page === 'list'): $rows=resource_rows($resource); ?>
<a class="btn btn-success mb-3" href="create.php">Créer</a>
<div class="table-responsive"><table class="table table-striped"><thead><tr>
<?php foreach ($resource['fields'] as $field): ?><th><?= h($field[0]) ?></th><?php endforeach; ?><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $row): $keys=array_intersect_key($row,array_flip($resource['keys'])); ?>
<tr><?php foreach ($resource['fields'] as $key=>$field): ?><td><?= h($row[$key]) ?></td><?php endforeach; ?>
<td><a class="btn btn-warning btn-sm" href="edit.php?<?= h(http_build_query($keys)) ?>">Modifier</a> <a class="btn btn-danger btn-sm" href="delete.php?<?= h(http_build_query($keys)) ?>">Supprimer</a></td></tr>
<?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="<?= count($resource['fields'])+1 ?>">Aucun élément.</td></tr><?php endif; ?>
</tbody></table></div>
<?php else: $action=['create'=>'create','edit'=>'update','delete'=>'delete'][$page]; ?>
<h2><?= ['create'=>'Créer','edit'=>'Modifier','delete'=>'Supprimer'][$page] ?></h2>
<form method="post" action="<?= h(BASE_URL.'/api/'.$entity.'/'.$action.'.php') ?>">
<input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
<?php if ($page !== 'create'): foreach ($resource['keys'] as $key): ?>
<input type="hidden" name="<?= h(($page==='edit'?'original_':'').$key) ?>" value="<?= h($record[$key]) ?>">
<?php endforeach; endif; ?>
<?php if ($page === 'delete'): ?>
<p>Confirmer la suppression de <?= h(implode(' — ',array_intersect_key($record,$resource['fields']))) ?> ? Les données dépendantes (albums, titres et favoris) seront également supprimées selon les relations de la base.</p>
<?php else: foreach ($resource['fields'] as $key=>$field): $value=$old[$key] ?? $record[$key] ?? ''; $value=is_scalar($value)?$value:''; ?>
<div class="mb-3"><label class="form-label" for="<?= h($key) ?>"><?= h($field[0]) ?></label>
<?php if ($field[1] === 'relation'): $target=resources()[$field[2]]; ?>
<select class="form-select" id="<?= h($key) ?>" name="<?= h($key) ?>" <?= empty($field[3])?'required':'' ?>>
<option value="">— Sélectionner —</option>
<?php foreach (resource_rows($target) as $choice): $id=$choice[$target['keys'][0]]; ?>
<option value="<?= h($id) ?>" <?= (string)$id===(string)$value?'selected':'' ?>><?= h(implode(' — ',array_filter(array_intersect_key($choice,$target['fields']),static fn($v)=>$v!==null && $v!==''))) ?></option>
<?php endforeach; ?></select>
<?php else: ?>
<input class="form-control" id="<?= h($key) ?>" name="<?= h($key) ?>" type="<?= h($field[1]) ?>" value="<?= h(is_scalar($value)?$value:'') ?>" <?= isset($field[2])?'maxlength="'.(int)$field[2].'"':'' ?> <?= $field[1]==='number'?'min="0.01" step="any"':'' ?> required>
<?php endif; ?></div>
<?php endforeach; ?>
<?php if ($entity === 'users'): ?>
<div class="mb-3"><label class="form-label" for="password">Mot de passe (facultatif, 12 caractères minimum)</label>
<input class="form-control" type="password" id="password" name="password" autocomplete="new-password">
<p class="form-text">Laisser vide conserve le mot de passe existant. Un nouveau compte sans mot de passe ne peut pas se connecter.</p></div>
<?php endif; endif; ?>
<button class="btn <?= $page==='delete'?'btn-danger':'btn-primary' ?>" type="submit"><?= $page==='delete'?'Confirmer la suppression':'Enregistrer' ?></button>
<a class="btn btn-secondary" href="list.php">Annuler</a>
</form>
<?php endif; ?></main>
<?php require ROOT.'/footer.php'; ?>
