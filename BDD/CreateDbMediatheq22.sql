/***************************/
/* 
// Mdiathèque (BD MySQL)
// @MB Le 20/09/26 13:17:00
*/
/***************************/

-- First we create the database

CREATE DATABASE MEDIATHEQ22
DEFAULT CHARACTER SET UTF8    -- Tous les formats de caractères
DEFAULT COLLATE utf8_general_ci ; --

-- SHOW VARIABLES;        -- Voir les paramètres de la BD

-- Flush / Init all privileges
FLUSH PRIVILEGES;

-- Now we create the Database

-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Client :  localhost:8889
-- Généré le :  Mer 20 septembre 2026 à 16:23
-- Version du serveur :  5.5.42
-- Version de PHP :  7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  MEDIATHEQ22
--
USE MEDIATHEQ22;

-- --------------------------------------------------------

--
-- Structure de la table ALBUM
--
/*=========================================*/
/* Table : ALBUM                           */
/*=========================================*/
create table ALBUM
(
   idAlb int(10) not null auto_increment,   -- PK
   idArt int(10) not null,                  -- FK
   idGp int(10) not null,                   -- FK
   nomA varchar(70),
   dtSortieA date,
   nomLabelA varchar(90),
   primary key (idAlb)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ------------------------------------------

/*=========================================*/
/* Index : ALBUM_FK                        */
/*=========================================*/
create index ALBUM_FK on ALBUM
(
   idAlb
);

-- ------------------------------------------

--
-- Structure de la table TITRE
--

/*=========================================*/
/* Table : TITRE                           */
/*=========================================*/
create table TITRE
(
   idTit int(10) not null auto_increment,   -- PK
   idAlb int(10) not null,                  -- FK
   nomTit varchar(70),
   dureeTit float,
   primary key (idTit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ------------------------------------------

/*=========================================*/
/* Index : TITRE_FK                        */
/*=========================================*/
create index TITRE_FK on TITRE
(
   idTit
);

-- ------------------------------------------

--
-- Structure de la table ARTISTE
--

/*=========================================*/
/* Table : ARTISTE                         */
/*=========================================*/
create table ARTISTE
(
   idArt int(10) not null auto_increment,   -- PK
   idGp int(10) not null,                   -- FK
   nomArt varchar(50),
   prenomArt varchar(50),
   primary key (idArt)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ------------------------------------------

/*=========================================*/
/* Index : ARTISTE_FK                      */
/*=========================================*/
create index ARTISTE_FK on ARTISTE
(
   idArt
);

-- ------------------------------------------

--
-- Structure de la table GROUPE
--

/*=========================================*/
/* Table : GROUPE                          */
/*=========================================*/
create table GROUPE
(
   idGp int(10) not null auto_increment,   -- PK
   nomGp varchar(50),
   dtCreaGp date,
   primary key (idGp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ------------------------------------------

/*=========================================*/
/* Index : GROUPE_FK                       */
/*=========================================*/
create index GROUPE_FK on GROUPE
(
   idGp
);

-- ------------------------------------------

--
-- Structure de la table USER
--

/*=========================================*/
/* Table : USER                            */
/*=========================================*/
create table USER
(
   eMailUser varchar(50) not null,   -- PK
   nomEUser varchar(50),
   prenomUser varchar(50),
   passUser varchar(255) not null,
   primary key (eMailUser)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*=========================================*/
/* Index : USER_FK                         */
/*=========================================*/
create index USER_FK on USER
(
   eMailUser
);

-- ------------------------------------------

--
-- Structure de la table LIKES (TJ)
--

/*=========================================*/
/* Table : LIKES                           */
/*=========================================*/
create table LIKES
(
   eMailUser varchar(50) not null,   -- PK, FK
   idAlb int(10) not null,           -- PK, FK
   primary key (eMailUser, idAlb)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*=========================================*/
/* Index : LIKES_FK                        */
/*=========================================*/
create index LIKES_FK on LIKES
(
   eMailUser
);

/*=========================================*/
/* Index : LIKES2_FK                       */
/*=========================================*/
create index LIKES2_FK on LIKES
(
   idAlb
);

-- --------------------------------------------------------------------

--
-- Contraintes pour les tables exportées
--

-- --------------------------------------------------------------------

alter table ALBUM add constraint FK_ASSOCIATION_0 foreign key (idArt)
      references ARTISTE (idArt) on delete cascade on update cascade;

-- --------------------------------------------------------------------

alter table TITRE add constraint FK_ASSOCIATION_1 foreign key (idAlb)
      references ALBUM (idAlb) on delete cascade on update cascade;

-- --------------------------------------------------------------------

alter table ALBUM add constraint FK_ASSOCIATION_2 foreign key (idGp)
      references GROUPE (idGp) on delete cascade on update cascade;

-- --------------------------------------------------------------------

alter table ARTISTE add constraint FK_ASSOCIATION_3 foreign key (idGp)
      references GROUPE (idGp) on delete cascade on update cascade;

-- --------------------------------------------------------------------

alter table LIKES add constraint FK_LIKES foreign key (idAlb)
      references ALBUM (idAlb) on delete cascade on update cascade;

alter table LIKES add constraint FK_LIKES2 foreign key (eMailUser)
      references USER (eMailUser) on delete cascade on update cascade;

-- --------------------------------------------------------------------

-- --------------------------------------------------------------------
-- --------------------------------------------------------------------
