<?php

/**
 * Normalise user input for later validation and safe HTML rendering.
 *
 * This helper is not an SQL-injection defence. Database values must always be
 * passed separately to a prepared PDO statement.
 */
function clean_input(mixed $value): mixed
{
    if (is_array($value)) {
        return array_map('clean_input', $value);
    }

    if (!is_string($value)) {
        return $value;
    }

    return htmlspecialchars(trim($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function generate_csrf_token(): string
{
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token = null): bool
{
    $token ??= isset($_POST['csrf_token']) && is_string($_POST['csrf_token'])
        ? $_POST['csrf_token']
        : null;

    return $token !== null
        && isset($_SESSION['csrf_token'])
        && is_string($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged(): bool
{
    return current_user() !== null;
}

function check_auth(): void
{
    if (is_logged()) {
        return;
    }

    $baseUrl = defined('ROOT_URL') ? rtrim(ROOT_URL, '/') : '';
    header('Location: ' . $baseUrl . '/views/backend/security/login.php');
    exit;
}

?>
