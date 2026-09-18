<?php
/** Exécute uniquement une requête SELECT écrite par l'application. */
function sql_select($query, array $parameters = array()){
    global $DB;

    //connect to database
    if(!isset($DB) || !$DB){
        sql_connect();
    }

    if (!preg_match('/^\s*SELECT\b/i', $query)) {
        throw new InvalidArgumentException('sql_select accepte uniquement une requête SELECT fixe.');
    }
    $request = $DB->prepare($query);
    $request->execute($parameters);
    return $request->fetchAll();
}
?>
