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
function valid_password(string $password): bool {
    return mb_strlen($password)>=12 && strlen($password)<=72 && preg_match('/[A-Z]/',$password) && preg_match('/[a-z]/',$password) && preg_match('/[0-9]/',$password) && preg_match('/[^a-zA-Z0-9\s]/u',$password);
}
function check_auth_rate(string $action): void {
    $now=time(); $window=intdiv($now,900);
    // Use the server-observed address, never a client supplied forwarding header.
    $bucket=hash('sha256',($action==='signup'?'signup':'login').'|'.($_SERVER['REMOTE_ADDR'] ?? 'unknown').'|'.$window);
    db()->prepare('DELETE FROM AUTH_RATE_LIMIT WHERE expiresAt < ?')->execute([$now]);
    db()->prepare('INSERT INTO AUTH_RATE_LIMIT (bucket,attempts,expiresAt) VALUES (?,1,?) ON DUPLICATE KEY UPDATE attempts=attempts+1')->execute([$bucket,($window+1)*900]);
    $attempts=query_rows('SELECT attempts FROM AUTH_RATE_LIMIT WHERE bucket = ?',[$bucket])[0]['attempts'];
    if ($attempts > ($action==='signup'?5:20)) {
        http_response_code(429); header('Retry-After: '.(($window+1)*900-$now));
        exit('Trop de tentatives. Réessayez dans quelques minutes.');
    }
}
