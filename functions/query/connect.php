<?php
//PDO connection
function sql_connect(){
    global $DB;

    //connect BDD with PDO using SQL_HOST, SQL_USER, SQL_PWD, SQL_DB
    // Avec encodage UTF8
    $DB = new PDO('mysql:host=' . SQL_HOST . ';charset=utf8;dbname=' . SQL_DB, SQL_USER, SQL_PWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $DB;
}
?>
