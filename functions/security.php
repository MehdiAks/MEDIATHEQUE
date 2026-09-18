<?php
// Check if user have access to ressource, take level needed and return boolean
function check_access($level) {
    // Le schéma USER ne définit pas de rôle : toute session authentifiée a le même accès.
    return !empty($_SESSION['USER_ID']) || !empty($_SESSION['id_user']) || !empty($_SESSION['eMailUser']);
}
?>
