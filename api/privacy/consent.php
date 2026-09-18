<?php
require dirname(__DIR__,2).'/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); header('Allow: POST'); exit; }
$token=$_POST['csrf_token'] ?? null;
if (!is_string($token) || !verify_csrf_token($token)) { http_response_code(403); exit('Jeton invalide.'); }
$choice=$_POST['choice'] ?? null;
if (!in_array($choice,['accepted','refused'],true)) { http_response_code(422); exit('Choix invalide.'); }
setcookie('mediatheque_consent',$choice,['expires'=>time()+180*86400,'path'=>'/','secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
redirect_to('/index.php');
