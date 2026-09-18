<?php
require_once dirname(__DIR__, 2) . '/config.php';
require_once dirname(__DIR__, 2) . '/functions/backend_crud.php';
backend_run('users', 'delete');
