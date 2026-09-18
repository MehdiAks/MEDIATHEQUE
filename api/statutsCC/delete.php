<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Jeton CSRF invalide.');
}
require_once '../../functions/ctrlSaisies.php';

$numStat = ($_POST['numStat']);

sql_delete('STATUT', 'numStat = :status_id', ['status_id' => $numStat]);


header('Location: ../../views/backend/statuts/list.php');
