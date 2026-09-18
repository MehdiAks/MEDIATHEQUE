<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/config.php';
require ROOT.'/functions/seo.php';
file_put_contents(ROOT.'/sitemap.xml',build_sitemap());
file_put_contents(ROOT.'/robots.txt',"User-agent: *\nAllow: /\nDisallow: /api/\nDisallow: /views/backend/\nDisallow: /scripts/\nDisallow: /tests/\nDisallow: /includes/\nDisallow: /functions/\nDisallow: /config/\nDisallow: /BDD/\nDisallow: /*?q=\nSitemap: ".BASE_URL."/sitemap.php\n");
echo "Sitemap XML et robots.txt générés.\n";
