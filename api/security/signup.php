<?php
header('Location: /views/backend/security/signup.php', true, $_SERVER['REQUEST_METHOD'] === 'POST' ? 307 : 303);
exit;
