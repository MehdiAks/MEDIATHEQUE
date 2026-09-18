<?php
/*
 * Endpoint API: api/likes/delete.php
 * Rôle: supprime (ou marque supprimé) un(e) like.
 *
 * Déroulé détaillé:
 * 1) Charge la configuration applicative et les helpers (session/DB/sanitisation).
 * 2) Récupère les paramètres POST (et éventuellement FILES) puis les nettoie via ctrlSaisies.
 * 3) Valide les contraintes métier (champs obligatoires, types, formats, tailles).
 * 4) Exécute la requête SQL adaptée (INSERT/UPDATE/DELETE) avec les valeurs préparées.
 * 5) Gère le feedback (flash/session/erreur) et redirige l'utilisateur vers l'écran cible.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('Jeton CSRF invalide.');
}
require_once '../../functions/ctrlSaisies.php';

$ba_bec_numMemb = ctrlSaisies($_POST['numMemb']);
$ba_bec_numArt = ctrlSaisies($_POST['numArt']);

// Suppression du like dans la base de données
sql_delete('LIKEART', 'numMemb = :member AND numArt = :artist', [
    'member' => $ba_bec_numMemb,
    'artist' => $ba_bec_numArt,
]);

// Redirection vers la liste des likes après suppression
header('Location: ../../views/backend/likes/list.php');
exit();
?>
