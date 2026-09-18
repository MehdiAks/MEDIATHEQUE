<?php
require __DIR__.'/config.php';
require_once ROOT.'/functions/seo.php';
header('Content-Type: application/xml; charset=utf-8');
echo build_sitemap();
