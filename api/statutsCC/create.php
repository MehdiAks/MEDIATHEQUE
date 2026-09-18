<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Jeton CSRF invalide.');
}
require_once '../../functions/ctrlSaisies.php';

$libStat = ($_POST['libStat']);

sql_insert('STATUT', 'libStat', ':label', ['label' => $libStat]);


header('Location: ../../views/backend/statuts/list.php');
