<?php
require_once dirname(__DIR__, 3) . '/config.php';
require_once dirname(__DIR__, 3) . '/functions/backend_crud.php';
backend_run('albums', 'list');
