<?php
define('SQL_HOST', getenv('DB_HOST'));
define('SQL_USER', getenv('DB_USER'));
define('SQL_PWD', getenv('DB_PASSWORD'));
define('SQL_DB', getenv('DB_DATABASE'));

// URL publique de l'application (sans slash final), configurable pour les
// installations placées dans un sous-répertoire.
define('BASE_URL', rtrim(getenv('BASE_URL') ?: ROOT_URL, '/'));
?>
