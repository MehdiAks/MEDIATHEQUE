<?php
header('Location: /views/backend/security/login.php', true, $_SERVER['REQUEST_METHOD'] === 'POST' ? 307 : 303);
exit;
