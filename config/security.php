<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function is_logged(): bool
{
    return isset($_SESSION['user'])
        && is_array($_SESSION['user'])
        && isset($_SESSION['user']['email']);
}

function check_auth(): void
{
    if (!is_logged()) {
        header('Location: /views/backend/security/login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
