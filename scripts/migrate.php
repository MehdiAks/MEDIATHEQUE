<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require dirname(__DIR__).'/config.php';
// Idempotent upgrades for an existing installation of the original schema.
$userColumns=array_column(query_rows('SHOW COLUMNS FROM `USER`'),'Field');
foreach (['passwordHash'=>'VARCHAR(255) NULL','isAdmin'=>'TINYINT(1) NOT NULL DEFAULT 0'] as $column=>$type) {
    if (!in_array($column,$userColumns,true)) db()->exec('ALTER TABLE `USER` ADD COLUMN '.$column.' '.$type);
}
if (!query_rows("SHOW COLUMNS FROM ALBUM LIKE 'imageA'")) db()->exec('ALTER TABLE ALBUM ADD COLUMN imageA VARCHAR(100) NULL');
db()->exec('ALTER TABLE ARTISTE MODIFY idGp INT(10) NULL');
db()->exec('ALTER TABLE ALBUM MODIFY idArt INT(10) NULL, MODIFY idGp INT(10) NULL');
db()->exec(file_get_contents(ROOT.'/BDD/004_antispam.sql'));
if (!query_rows("SHOW COLUMNS FROM TITRE LIKE 'audioTit'")) db()->exec(file_get_contents(ROOT.'/BDD/005_track_audio.sql'));
echo "Base de données à jour.\n";
