-- À exécuter une seule fois sur une base créée avec CreateDbMediatheq22.sql.
ALTER TABLE `USER`
    ADD COLUMN passwordHash VARCHAR(255) NULL,
    ADD COLUMN isAdmin TINYINT(1) NOT NULL DEFAULT 0;
-- Un artiste peut être solo et un album peut appartenir à un artiste ou un groupe.
ALTER TABLE ARTISTE MODIFY idGp INT(10) NULL;
ALTER TABLE ALBUM MODIFY idArt INT(10) NULL, MODIFY idGp INT(10) NULL;
