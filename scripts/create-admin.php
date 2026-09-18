<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/config.php';
$email=$argv[1] ?? '';
if (!filter_var($email,FILTER_VALIDATE_EMAIL) || strlen($email)>50) exit("Usage : php scripts/create-admin.php email\nLe mot de passe est lu sur l’entrée standard.\n");
fwrite(STDOUT,"Mot de passe (12 caractères minimum) : ");
$password=rtrim(fgets(STDIN),"\r\n");
if (strlen($password)<12 || strlen($password)>72) exit("Longueur invalide.\n");
$stmt=db()->prepare('INSERT INTO `USER` (eMailUser,nomEUser,prenomUser,passwordHash,isAdmin) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE passwordHash=VALUES(passwordHash), isAdmin=1');
$stmt->execute([$email,'Administrateur','Médiathèque',password_hash($password,PASSWORD_DEFAULT)]);
echo "Compte administrateur prêt.\n";
