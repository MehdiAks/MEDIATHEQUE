<?php
// insert instance
function sql_insert($query, array $parameters = array()){
    global $DB;

    //connect to database
    if(!isset($DB) || !$DB){
        sql_connect();
    }

    if (!preg_match('/^\s*INSERT\b/i', $query)) throw new InvalidArgumentException('Requête INSERT attendue.');
    $request = $DB->prepare($query);
    return $request->execute($parameters);
}
?>
