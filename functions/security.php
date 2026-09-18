<?php
function current_user(): ?array {
    $email = $_SESSION['eMailUser'] ?? null;
    if (!is_string($email)) return null;
    return query_rows('SELECT eMailUser, nomEUser, prenomUser, isAdmin FROM `USER` WHERE eMailUser = ?', [$email])[0] ?? null;
}
function check_access($level = 1): bool { $user = current_user(); return $user && ($level > 1 || (bool)$user['isAdmin']); }
function require_admin(): void {
    if (!current_user()) redirect_to('/views/backend/security/login.php');
    if (!check_access()) { http_response_code(403); exit('Accès réservé à l’administration.'); }
}
