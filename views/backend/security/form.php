<?php require_once dirname(__DIR__,3).'/config.php'; require ROOT.'/header.php'; ?>
<main class="auth-page"><section class="auth-card"><img class="auth-michel" src="<?= h(BASE_URL.'/src/images/Michel.png') ?>" alt="Michel"><p class="eyebrow dark">Bienvenue à la médiathèque</p><h1><?= $action==='signup'?'Créer un compte':'Connexion' ?></h1>
<?php flashes(); ?>
<form data-auth="<?= h($action) ?>" method="post" action="<?= h(BASE_URL.'/api/security/'.$action.'.php') ?>">
<input type="hidden" name="csrf_token" value="<?= h(generate_csrf_token()) ?>">
<?php if ($action==='signup'): ?>
<div class="spam-trap" aria-hidden="true"><label for="website">Laisser ce champ vide</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
<div class="mb-3"><label for="nom">Nom</label><input class="form-control" id="nom" name="nomEUser" maxlength="50" autocomplete="family-name" required></div>
<div class="mb-3"><label for="prenom">Prénom</label><input class="form-control" id="prenom" name="prenomUser" maxlength="50" autocomplete="given-name" required></div>
<?php endif; ?>
<div class="mb-3"><label for="email">Adresse e-mail</label><input class="form-control" type="email" id="email" name="eMailUser" maxlength="50" autocomplete="email" aria-describedby="email-help" required><p id="email-help" class="field-help" aria-live="polite">Format attendu : texte@texte.texte</p></div>
<div class="mb-3"><label for="password">Mot de passe<?= $action==='signup'?' (12 caractères minimum)':'' ?></label><input class="form-control" type="password" id="password" name="password" <?= $action==='signup'?'aria-describedby="password-rules"':'' ?> autocomplete="<?= $action==='signup'?'new-password':'current-password' ?>" required></div>
<?php if ($action==='signup'): ?>
<ul id="password-rules" class="password-rules" aria-live="polite">
<?php foreach (['length'=>'Au moins 12 caractères','upper'=>'Une majuscule (A–Z)','lower'=>'Une minuscule (a–z)','digit'=>'Un chiffre','special'=>'Un caractère spécial','bytes'=>'72 octets maximum'] as $rule=>$label): ?><li data-rule="<?= $rule ?>"><span aria-hidden="true">○</span> <?= $label ?></li><?php endforeach; ?></ul>
<div class="mb-3"><label for="confirm">Confirmer le mot de passe</label><input class="form-control" type="password" id="confirm" name="password_confirm" autocomplete="new-password" aria-describedby="confirm-help" required><p id="confirm-help" class="field-help" aria-live="polite"></p></div>
<p><label><input type="checkbox" name="consent" value="1" required> J’accepte les <a href="<?= h(BASE_URL.'/views/frontend/rgpd/cgu.php') ?>">conditions d’utilisation</a>.</label></p>
<?php endif; ?>
<button class="btn btn-primary" type="submit"><?= $action==='signup'?'Créer mon compte':'Se connecter' ?></button>
</form><p class="auth-switch"><a href="<?= $action==='signup'?'login.php':'signup.php' ?>"><?= $action==='signup'?'Déjà un compte ? Se connecter':'Créer un compte' ?></a></p></section></main>
<?php require ROOT.'/footer.php'; ?>
