<?php
require_once dirname(__DIR__,2).'/config.php';
redirect_to('/index.php?'.http_build_query(['q'=>is_string($_GET['q'] ?? null)?$_GET['q']:'']));
