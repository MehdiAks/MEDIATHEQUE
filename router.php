<?php
// Router for PHP's development server; Apache uses .htaccess.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
if (preg_match('~(?:^|/)(?:\.[^/]*|BDD|tests|scripts|functions|config|includes)(?:/|$)|\.(?:sql|log)$|/README[^/]*$~i', $path)) {
    http_response_code(403);
    exit('Accès interdit.');
}
return false;
