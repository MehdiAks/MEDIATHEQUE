<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /', true, 303);
    exit;
}

header('Location: /views/backend/security/logout.php', true, 307);
exit;
