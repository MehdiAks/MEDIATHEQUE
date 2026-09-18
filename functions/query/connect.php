<?php
function sql_connect(){
    global $DB;
    if ($DB instanceof PDO) return $DB;
    $dsn = 'mysql:host='.SQL_HOST.';port='.(getenv('DB_PORT') ?: '3306').';charset=utf8mb4;dbname='.SQL_DB;
    if (getenv('DB_SOCKET')) $dsn = 'mysql:unix_socket='.getenv('DB_SOCKET').';charset=utf8mb4;dbname='.SQL_DB;
    $DB = new PDO($dsn, SQL_USER, SQL_PWD, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
    return $DB;
}
