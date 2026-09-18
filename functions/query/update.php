<?php
// update instance
function sql_update($query, array $parameters = array()) {
    global $DB;

    //connect to database
    if(!isset($DB) || !$DB){
        sql_connect();
    }

    if (!preg_match('/^\s*UPDATE\b/i', $query)) throw new InvalidArgumentException('Requête UPDATE attendue.');
    $request = $DB->prepare($query);
    return $request->execute($parameters);
}
?>
