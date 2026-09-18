<?php
// delete instance
function sql_delete($query, array $parameters = array()){
    global $DB;

    //connect to database
    if(!isset($DB) || !$DB){
        sql_connect();
    }

    if (!preg_match('/^\s*DELETE\b/i', $query)) throw new InvalidArgumentException('Requête DELETE attendue.');
    $request = $DB->prepare($query);
    return $request->execute($parameters);
}
?>
