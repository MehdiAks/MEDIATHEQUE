<?php
/**
 * Chargeur global de fonctions.
 * Ce fichier centralise les require_once pour rendre les helpers disponibles.
 */

// Charge les fonctions liées aux requêtes SQL (dans functions/query/load.php).
require_once __DIR__ . '/query/load.php';
// Charge les helpers de sécurité (auth, vérifications, etc.).
require_once __DIR__ . '/security.php';
// Charge les utilitaires divers (cURL, BBCode, etc.).
require_once __DIR__ . '/various.php';
// Charge les fonctions liées aux données (si elles existent).
require_once __DIR__ . '/data.php';
// Charge les helpers de messages flash (notifications utilisateur).
require_once __DIR__ . '/flash.php';

?>
