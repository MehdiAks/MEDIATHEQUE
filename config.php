<?php
//define ROOT_PATH
define('ROOT', $_SERVER['DOCUMENT_ROOT']);
define('ROOT_URL', 'http://' . $_SERVER['HTTP_HOST']);

//Load env
require_once ROOT . '/includes/libs/DotEnv.php';
(new DotEnv(ROOT.'/.env'))->load();

//defines
require_once ROOT . '/config/defines.php';

// Configure error handling in every environment (production included).
require_once ROOT . '/config/debug.php';

// The central configuration owns session initialisation.  Individual pages and
// endpoints must not start a second, independently configured session.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//load functions
require_once ROOT . '/functions/global.inc.php';

//load security
require_once ROOT . '/config/security.php';
