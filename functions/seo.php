<?php
function build_sitemap(): string {
    $urls=['/index.php','/views/frontend/rgpd/rgpd.php','/views/frontend/rgpd/cgu.php'];
    foreach (query_rows('SELECT idAlb FROM ALBUM ORDER BY idAlb') as $album) $urls[]='/album.php?id='.(int)$album['idAlb'];
    $xml='<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach ($urls as $url) $xml.='<url><loc>'.htmlspecialchars(BASE_URL.$url,ENT_XML1|ENT_QUOTES,'UTF-8').'</loc></url>';
    return $xml.'</urlset>';
}
