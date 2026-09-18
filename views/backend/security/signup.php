<?php
require_once dirname(__DIR__, 3) . '/config.php';

if (is_logged()) {
    header('Location: /views/backend/dashboard.php');
    exit;
}

$errors = [];
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Le formulaire a expiré. Veuillez réessayer.';
    }
    if (!preg_match("/^[\\p{L}][\\p{L} '\\-]{1,49}$/u", $nom)) {
        $errors[] = 'Le nom doit contenir entre 2 et 50 caractères valides.';
    }
    if (!preg_match("/^[\\p{L}][\\p{L} '\\-]{1,49}$/u", $prenom)) {
        $errors[] = 'Le prénom doit contenir entre 2 et 50 caractères valides.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        $errors[] = 'L’adresse email est invalide.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }
    if ($password !== $passwordConfirmation) {
        $errors[] = 'La confirmation du mot de passe ne correspond pas.';
    }

    if (!$errors) {
        $database = sql_connect();
        $duplicate = $database->prepare('SELECT 1 FROM USER WHERE eMailUser = :email LIMIT 1');
        $duplicate->execute(['email' => $email]);

        if ($duplicate->fetchColumn()) {
            $errors[] = 'Un compte existe déjà avec cette adresse email.';
        } else {
            $insert = $database->prepare(
                'INSERT INTO USER (eMailUser, nomEUser, prenomUser, passUser) VALUES (:email, :nom, :prenom, :password)'
            );
            $insert->execute([
                'email' => $email,
                'nom' => $nom,
                'prenom' => $prenom,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            header('Location: /views/backend/security/login.php?registered=1');
            exit;
        }
    }
}

include dirname(__DIR__, 3) . '/header.php';
?>
<main class="container py-5">
    <h1>Inscription</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert"><ul class="mb-0">
            <?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?>
        </ul></div>
    <?php endif; ?>
    <form method="post" class="col-md-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php foreach (['prenom' => 'Prénom', 'nom' => 'Nom'] as $field => $label): ?>
            <div class="mb-3"><label class="form-label" for="<?= $field ?>"><?= $label ?></label>
            <input class="form-control" id="<?= $field ?>" name="<?= $field ?>" maxlength="50" required value="<?= htmlspecialchars($$field, ENT_QUOTES, 'UTF-8') ?>"></div>
        <?php endforeach; ?>
        <div class="mb-3"><label class="form-label" for="email">Adresse email</label>
        <input class="form-control" id="email" name="email" type="email" maxlength="50" autocomplete="email" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="mb-3"><label class="form-label" for="password">Mot de passe</label>
        <input class="form-control" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required></div>
        <div class="mb-3"><label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required></div>
        <button class="btn btn-primary" type="submit">Créer mon compte</button>
    </form>
</main>

