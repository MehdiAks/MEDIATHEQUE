<?php
require_once dirname(__DIR__,2).'/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Allow: POST'); http_response_code(405); exit; }
$token=$_POST['csrf_token'] ?? null;
if (!is_string($token) || !verify_csrf_token($token)) { http_response_code(403); exit('Jeton CSRF invalide.'); }
if ($action === 'disconnect') {
    $_SESSION=[]; session_regenerate_id(true); redirect_to('/index.php');
}
try {
    check_auth_rate($action);
    if ($action === 'signup' && ($_POST['website'] ?? '') !== '') throw new InvalidArgumentException('Inscription refusée. Veuillez réessayer.');
    $email=$_POST['eMailUser'] ?? ''; $password=$_POST['password'] ?? '';
    if (!is_string($email) || (!filter_var($email,FILTER_VALIDATE_EMAIL) || !preg_match('/^[^\s@]+@[^\s@.]+(?:\.[^\s@.]+)+$/D',$email)) || strlen($email)>50 || !is_string($password)) throw new InvalidArgumentException('Identifiants invalides.');
    $email=trim($email);
    if ($action === 'signup') {
        if (!valid_password($password) || $password !== ($_POST['password_confirm'] ?? null)) throw new InvalidArgumentException('Utilisez au moins 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (72 octets maximum), avec une confirmation identique.');
        if (($_POST['consent'] ?? '') !== '1') throw new InvalidArgumentException('Veuillez accepter les conditions d’utilisation.');
        $data=validate_resource(resources()['users'],$_POST);
        $stmt=db()->prepare('INSERT INTO `USER` (eMailUser,nomEUser,prenomUser,passwordHash,isAdmin) VALUES (?,?,?,?,0)');
        $stmt->execute([$email,$data['nomEUser'],$data['prenomUser'],password_hash($password,PASSWORD_DEFAULT)]);
    } else {
        $user=query_rows('SELECT * FROM `USER` WHERE eMailUser = ?',[$email])[0] ?? null;
        if (!$user || !$user['passwordHash'] || !password_verify($password,$user['passwordHash'])) throw new InvalidArgumentException('Adresse e-mail ou mot de passe incorrect.');
        $email=$user['eMailUser'];
    }
    session_regenerate_id(true); $_SESSION['eMailUser']=$_SESSION['USER_ID']=$email; unset($_SESSION['csrf_token']);
    redirect_to(check_access() ? '/views/backend/dashboard.php' : '/index.php');
} catch (InvalidArgumentException $e) { $_SESSION['messages']=[$e->getMessage()]; }
catch (PDOException $e) { error_log((string)$e); $code=(int)($e->errorInfo[1] ?? 0);
    $_SESSION['messages']=[match ($code) {
        1062 => 'Cette adresse e-mail est déjà utilisée. Connectez-vous avec votre compte.',
        1054,1146 => 'La base de données du site doit être mise à jour. Demandez à la personne qui installe le site de lancer php scripts/migrate.php.',
        default => 'La connexion à la base de données est indisponible. Vérifiez la configuration .env du serveur ou contactez son administrateur.',
    }]; }
redirect_to('/views/backend/security/'.$action.'.php');
