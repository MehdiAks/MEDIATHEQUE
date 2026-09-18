<?php
require_once dirname(__DIR__, 3) . '/config.php';

if (is_logged()) {
    header('Location: /views/backend/dashboard.php');
    exit;
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Le formulaire a expiré. Veuillez réessayer.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors[] = 'Renseignez une adresse email et un mot de passe valides.';
    } else {
        $statement = sql_connect()->prepare(
            'SELECT eMailUser, nomEUser, prenomUser, passUser FROM USER WHERE eMailUser = :email LIMIT 1'
        );
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['passUser'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'email' => $user['eMailUser'],
                'nom' => $user['nomEUser'],
                'prenom' => $user['prenomUser'],
            ];
            unset($_SESSION['csrf_token']);
            header('Location: /views/backend/dashboard.php');
            exit;
        }

        $errors[] = 'Adresse email ou mot de passe incorrect.';
    }
}

include dirname(__DIR__, 3) . '/header.php';
?>
<main class="container py-5">
    <h1>Connexion</h1>
    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert"><?= htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" class="col-md-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="mb-3">
            <label class="form-label" for="email">Adresse email</label>
            <input class="form-control" id="email" name="email" type="email" maxlength="50" autocomplete="email" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required>
        </div>
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </form>
</main>

