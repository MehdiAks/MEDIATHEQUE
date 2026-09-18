<?php require_once dirname(__DIR__,3).'/config.php'; require ROOT.'/header.php'; ?>
<main class="container py-4"><h1><?= $action==='signup'?'Créer un compte':'Connexion' ?></h1>
<?php flashes(); ?>
<form method="post" action="<?= h(BASE_URL.'/api/security/'.$action.'.php') ?>">
<input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
<?php if ($action==='signup'): ?>
<div class="mb-3"><label for="nom">Nom</label><input class="form-control" id="nom" name="nomEUser" maxlength="50" autocomplete="family-name" required></div>
<div class="mb-3"><label for="prenom">Prénom</label><input class="form-control" id="prenom" name="prenomUser" maxlength="50" autocomplete="given-name" required></div>
<?php endif; ?>
<div class="mb-3"><label for="email">Adresse e-mail</label><input class="form-control" type="email" id="email" name="eMailUser" maxlength="50" autocomplete="email" required></div>
<div class="mb-3"><label for="password">Mot de passe<?= $action==='signup'?' (12 caractères minimum)':'' ?></label><input class="form-control" type="password" id="password" name="password" autocomplete="<?= $action==='signup'?'new-password':'current-password' ?>" required></div>
<?php if ($action==='signup'): ?>
<div class="mb-3"><label for="confirm">Confirmer le mot de passe</label><input class="form-control" type="password" id="confirm" name="password_confirm" autocomplete="new-password" required></div>
<p><label><input type="checkbox" name="consent" value="1" required> J’accepte les <a href="<?= h(BASE_URL.'/views/frontend/rgpd/cgu.php') ?>">conditions d’utilisation</a>.</label></p>
<?php endif; ?>
<button class="btn btn-primary" type="submit"><?= $action==='signup'?'Créer mon compte':'Se connecter' ?></button>
</form></main>
<?php require ROOT.'/footer.php'; ?>
