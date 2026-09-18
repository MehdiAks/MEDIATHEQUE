<?php
//PDO connection
function sql_connect(){
    global $DB;

    if (!$DB instanceof PDO) {
        $DB = new PDO(
            'mysql:host=' . SQL_HOST . ';charset=utf8;dbname=' . SQL_DB,
            SQL_USER,
            SQL_PWD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    return $DB;
}
?>
