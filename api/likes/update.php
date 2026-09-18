<?php
/*
 * Endpoint API: api/likes/update.php
 * Rôle: met à jour un(e) like existant(e).
 *
 * Déroulé détaillé:
 * 1) Charge la configuration applicative et les helpers (session/DB/sanitisation).
 * 2) Récupère les paramètres POST (et éventuellement FILES) puis les nettoie via ctrlSaisies.
 * 3) Valide les contraintes métier (champs obligatoires, types, formats, tailles).
 * 4) Exécute la requête SQL adaptée (INSERT/UPDATE/DELETE) avec les valeurs préparées.
 * 5) Gère le feedback (flash/session/erreur) et redirige l'utilisateur vers l'écran cible.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

$ba_bec_numMemb = ctrlSaisies($_POST['numMemb']);
$ba_bec_numArt = ctrlSaisies($_POST['numArt']);
$ba_bec_likeA = isset($_POST['likeA']) ? ctrlSaisies($_POST['likeA']) : "0";

if ($ba_bec_likeA !== "1" && $ba_bec_likeA !== "0") {
    die("Erreur : valeur de like invalide.");
}
$ba_bec_likeA = (int) $ba_bec_likeA;

// Mise à jour du like dans la base de données
sql_update(
    'LIKEART', 
    'likeA = ' . $ba_bec_likeA,
    'numMemb = ' . $ba_bec_numMemb . ' AND numArt = ' . $ba_bec_numArt
);

// Redirection vers la liste des likes après modification
header('Location: ../../views/backend/likes/list.php');
exit();
?>
