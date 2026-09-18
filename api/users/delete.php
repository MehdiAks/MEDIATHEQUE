<?php
/*
 * Endpoint API: api/members/delete.php
 * Rôle: supprime (ou marque supprimé) un(e) member.
 *
 * Déroulé détaillé:
 * 1) Charge la configuration applicative et les helpers (session/DB/sanitisation).
 * 2) Récupère les paramètres POST (et éventuellement FILES) puis les nettoie via ctrlSaisies.
 * 3) Valide les contraintes métier (champs obligatoires, types, formats, tailles).
 * 4) Exécute la requête SQL adaptée (INSERT/UPDATE/DELETE) avec les valeurs préparées.
 * 5) Gère le feedback (flash/session/erreur) et redirige l'utilisateur vers l'écran cible.
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once '../../functions/ctrlSaisies.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/backend/members/list.php');
    exit();
}

$ba_bec_numMemb = ctrlSaisies($_POST['numMemb'] ?? '');
$ba_bec_redirectUrl = '../../views/backend/members/delete.php';
if (!empty($ba_bec_numMemb)) {
    $ba_bec_redirectUrl .= '?numMemb=' . urlencode($ba_bec_numMemb);
}

$ba_bec_recaptcha = verifyRecaptcha($_POST['g-recaptcha-response'] ?? '', 'delete');
if (!$ba_bec_recaptcha['valid']) {
    $_SESSION['errors'] = [$ba_bec_recaptcha['message'] ?: 'Échec de la vérification reCAPTCHA.'];
    header('Location: ' . $ba_bec_redirectUrl);
    exit();
}

if (empty($ba_bec_numMemb)) {
    $_SESSION['errors'] = ['ID du membre manquant.'];
    header('Location: ' . $ba_bec_redirectUrl);
    exit();
}

$ba_bec_delete_result = sql_delete('MEMBRE', "numMemb = $ba_bec_numMemb");
if ($ba_bec_delete_result['success']) {
    flash_success();
} elseif (!empty($ba_bec_delete_result['constraint']) || sql_is_foreign_key_error($ba_bec_delete_result['message'] ?? '', $ba_bec_delete_result['code'] ?? null)) {
    flash_delete_impossible('Suppression impossible : ce membre est utilisé dans d’autres données.');
} else {
    flash_error();
}

header('Location: ../../views/backend/members/list.php');

?>
