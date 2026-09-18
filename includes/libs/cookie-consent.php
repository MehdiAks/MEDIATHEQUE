<?php
/**
 * GESTION DU CONSENTEMENT COOKIES (RGPD)
 * Ce script permet de synchroniser le choix de l'utilisateur entre son navigateur (Cookie)
 * et son compte utilisateur en base de données (Table membre).
 */

// Initialisation de la session pour identifier si un utilisateur est connecté.
// On ne démarre la session que si elle n'existe pas encore afin d'éviter les warnings PHP.
// Cette session sert ensuite à récupérer l'identifiant de l'utilisateur (user_id) pour
// savoir si on doit lire/écrire le consentement en base de données.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuration : Nom du cookie et durée de validité (ici 1 an).
// Ces constantes sont utilisées dans toutes les fonctions pour éviter la duplication
// et garantir la cohérence entre la lecture/écriture du cookie.
if (!defined('COOKIE_DURATION')) {
    define('COOKIE_DURATION', 365 * 24 * 60 * 60);
}

if (!defined('COOKIE_NAME')) {
    define('COOKIE_NAME', 'bec_cookie_consent');
}

/* ==========================================================================
   OUTILS : Fonctions utilitaires
   ========================================================================== */

/**
 * Crée ou met à jour le cookie physique sur le navigateur de l'utilisateur.
 * Le cookie contient la valeur de consentement (0/1) et est configuré pour être
 * accessible sur tout le domaine, avec un flag HttpOnly pour limiter l'accès via JS.
 * @param int $consent (0 pour refus, 1 pour acceptation)
 */
function setConsentCookie(int $consent) {
    // setcookie() envoie un en-tête HTTP : il faut donc l'appeler avant tout output.
    // La valeur est convertie en string car les cookies sont stockés en texte.
    setcookie(
        COOKIE_NAME,
        (string) $consent,
        time() + COOKIE_DURATION, // Expiration : maintenant + 1 an
        '/',                      // Disponible sur tout le domaine
        '',                       // Domaine vide = domaine actuel
        false,                    // Secure : false (devrait être true si HTTPS)
        true                      // HttpOnly : true (protection contre les failles XSS/JS)
    );
}

/* ==========================================================================
   LECTURE : Récupération du choix actuel
   ========================================================================== */

/**
 * Récupère le consentement en priorité depuis la BDD (si connecté) sinon via cookie.
 * Logique générale :
 * - Si l'utilisateur est connecté, la BDD est la source de vérité.
 * - Si la BDD n'a pas de valeur, on tente de synchroniser depuis le cookie navigateur.
 * - Si l'utilisateur est anonyme, le cookie navigateur est la seule source disponible.
 * @param PDO|null $pdo Connexion à la base de données
 */
function getCookieConsent($pdo) {
    // Sécurité : Si l'objet PDO n'est pas valide, on ne regarde que le cookie navigateur
    if (!$pdo instanceof PDO) {
        // Sans BDD, on retourne uniquement le cookie s'il existe.
        if (isset($_COOKIE[COOKIE_NAME])) {
            return (int) $_COOKIE[COOKIE_NAME];
        }
        return null; // Aucun choix n'a encore été fait
    }

    // --- CAS 1 : L'UTILISATEUR EST CONNECTÉ ---
    if (!empty($_SESSION['user_id'])) {
        try {
            // 1. On cherche d'abord la valeur stockée dans son profil membre
            $stmt = $pdo->prepare("SELECT cookieMemb FROM membre WHERE numMemb = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $memberConsent = $stmt->fetchColumn();

            // Si une valeur existe en BDD (différente de vide ou false), on la retourne
            if ($memberConsent !== false && $memberConsent !== null && $memberConsent !== '') {
                // Valeur BDD trouvée : elle prime sur le cookie navigateur.
                return (int) $memberConsent;
            }

            // 2. Si rien en BDD mais qu'un cookie existe sur le navigateur, on synchronise
            if (isset($_COOKIE[COOKIE_NAME])) {
                $cookieConsent = (int) $_COOKIE[COOKIE_NAME];

                // On enregistre le choix du cookie dans le compte du membre
                // pour qu'il soit persisté côté serveur (multi-appareils).
                $stmt = $pdo->prepare(
                    "UPDATE membre 
                     SET cookieMemb = ?, dtMajMemb = NOW() 
                     WHERE numMemb = ?"
                );
                $stmt->execute([$cookieConsent, $_SESSION['user_id']]);

                return $cookieConsent;
            }
        } catch (Throwable $exception) {
            // En production, certaines BDD n'ont pas encore la colonne cookieMemb.
            // On revient alors à une lecture uniquement via cookie pour éviter une page blanche.
            if (isset($_COOKIE[COOKIE_NAME])) {
                return (int) $_COOKIE[COOKIE_NAME];
            }

            return null;
        }

        // Ni cookie navigateur ni valeur BDD : l'utilisateur n'a pas encore choisi.
        return null;
    }

    // --- CAS 2 : VISITEUR ANONYME (NON CONNECTÉ) ---
    // On se base uniquement sur le cookie du navigateur
    if (isset($_COOKIE[COOKIE_NAME])) {
        // Visiteur non connecté : la valeur est uniquement celle stockée localement.
        return (int) $_COOKIE[COOKIE_NAME];
    }

    return null; // Le visiteur n'a pas encore fait de choix
}

/* ==========================================================================
   SAUVEGARDE : Enregistrement d'un nouveau choix
   ========================================================================== */

/**
 * Enregistre le choix de l'utilisateur (Acceptation ou Refus).
 * La logique diffère selon que l'utilisateur soit connecté :
 * - Connecté : on persiste en BDD + on écrit le cookie.
 * - Anonyme : on écrit uniquement le cookie.
 * @param PDO|null $pdo
 * @param int $consent Valeur du consentement
 */
function saveCookieConsent($pdo, int $consent) {
    // Si la base de données est indisponible, on arrête tout
    if (!$pdo instanceof PDO) {
        return;
    }

    // --- SI CONNECTÉ : Sauvegarde BDD + Cookie ---
    if (!empty($_SESSION['user_id'])) {
        try {
            // Mise à jour du profil membre pour conserver une trace durable côté serveur.
            $stmt = $pdo->prepare(
                "UPDATE membre 
                 SET cookieMemb = ?, dtMajMemb = NOW() 
                 WHERE numMemb = ?"
            );
            $stmt->execute([$consent, $_SESSION['user_id']]);
        } catch (Throwable $exception) {
            // Si la colonne BDD n'existe pas encore, on continue sans bloquer la navigation.
        }

        // On crée aussi le cookie pour que le choix soit persistant côté client
        setConsentCookie($consent);
        return;
    }

    // --- SI ANONYME : Sauvegarde Cookie uniquement ---
    // Ici, pas de BDD liée à un compte : on se contente de stocker dans le navigateur.
    setConsentCookie($consent);
}
