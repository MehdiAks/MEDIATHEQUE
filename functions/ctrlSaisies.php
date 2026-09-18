<?php
// Fonction de contrôle des saisies avant insertion en base de données (BDD).
function ctrlSaisies($saisie){
    // Si la saisie est nulle, on retourne une chaîne vide pour éviter les erreurs.
    if ($saisie === null) {
        return '';
    }

    // Convertit les caractères spéciaux en entités HTML pour éviter l'injection HTML.
    // ENT_QUOTES convertit aussi les guillemets simples et doubles.
    $saisie = htmlspecialchars((string) $saisie, ENT_QUOTES);
    // Supprime les espaces et caractères invisibles en début et fin de chaîne.
    $saisie = trim($saisie);
    // Retire les antislashs ajoutés par magic_quotes (si activé dans l'environnement).
    $saisie = stripslashes($saisie);
    // Retourne la saisie nettoyée et sécurisée.
    return $saisie;
}
?>
