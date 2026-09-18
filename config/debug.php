<?php

error_reporting(E_ALL);

$debugEnabled = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL);
$environment = strtolower((string) (getenv('APP_ENV') ?: 'production'));
$displayErrors = $debugEnabled || in_array($environment, ['dev', 'development', 'local', 'test'], true);

ini_set('display_errors', $displayErrors ? '1' : '0');
ini_set('display_startup_errors', $displayErrors ? '1' : '0');

/** Display a debug value without allowing it to inject HTML. */
function dump(mixed $value): void
{
    $rendered = print_r($value, true);
    echo '<pre>' . htmlspecialchars($rendered, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
}

/** Display debug values safely, then stop execution. */
function dd(mixed ...$values): never
{
    foreach ($values as $value) {
        dump($value);
    }

    exit;
}
