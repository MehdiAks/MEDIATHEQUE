<?php
//define ROOT_PATH
define('ROOT', __DIR__);
define('ROOT_URL', ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost'));

//Load env
require_once ROOT . '/includes/libs/DotEnv.php';
if (is_file(ROOT.'/.env')) (new DotEnv(ROOT.'/.env'))->load();

//defines
require_once ROOT . '/config/defines.php';

// Configure error handling in every environment (production included).
require_once ROOT . '/config/debug.php';

// The central configuration owns session initialisation.  Individual pages and
// endpoints must not start a second, independently configured session.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly'=>true, 'samesite'=>'Lax', 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off']);
    session_start();
}

//load functions
require_once ROOT . '/functions/global.inc.php';

//load security
require_once ROOT . '/config/security.php';
