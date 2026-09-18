-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 18 sep. 2026 à 10:46
-- Version du serveur : 8.0.44
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `MEDIATHEQ22`
--

-- --------------------------------------------------------

--
-- Structure de la table `ALBUM`
--

CREATE TABLE `ALBUM` (
  `idAlb` int NOT NULL,
  `idArt` int DEFAULT NULL,
  `idGp` int DEFAULT NULL,
  `nomA` varchar(70) DEFAULT NULL,
  `dtSortieA` date DEFAULT NULL,
  `nomLabelA` varchar(90) DEFAULT NULL,
  `imageA` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `ALBUM`
--

INSERT INTO `ALBUM` (`idAlb`, `idArt`, `idGp`, `nomA`, `dtSortieA`, `nomLabelA`, `imageA`) VALUES
(1, 1, 1, 'mmi', '2001-03-12', 'rap', 'uploads/albums/a6f1ba4a0662cfc396300c189e9f4686.jpg'),
(2, NULL, 2, 'OUR DIFFERENCES', '2026-09-18', 'CHANSON DU MONDE', 'uploads/albums/50cfead65d1e1937dcca26b000a590ea.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `ARTISTE`
--

CREATE TABLE `ARTISTE` (
  `idArt` int NOT NULL,
  `idGp` int DEFAULT NULL,
  `nomArt` varchar(50) DEFAULT NULL,
  `prenomArt` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `ARTISTE`
--

INSERT INTO `ARTISTE` (`idArt`, `idGp`, `nomArt`, `prenomArt`) VALUES
(1, 1, 'afankous', 'mehdoche'),
(2, 2, 'Pequenio', 'Martin'),
(3, 2, 'BTS', 'NGY'),
(4, 2, 'MELOVE', 'JOUF'),
(5, 2, 'Douroustigno', 'La bagnana');

-- --------------------------------------------------------

--
-- Structure de la table `AUTH_RATE_LIMIT`
--

CREATE TABLE `AUTH_RATE_LIMIT` (
  `bucket` char(64) NOT NULL,
  `attempts` int UNSIGNED NOT NULL DEFAULT '1',
  `expiresAt` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `AUTH_RATE_LIMIT`
--

INSERT INTO `AUTH_RATE_LIMIT` (`bucket`, `attempts`, `expiresAt`) VALUES
('21ddc6bcabe7babba3cf288a8af71e04ac7570e5dd0e532fbf7d1f23177c9e13', 1, 1789725600);

-- --------------------------------------------------------

--
-- Structure de la table `GROUPE`
--

CREATE TABLE `GROUPE` (
  `idGp` int NOT NULL,
  `nomGp` varchar(50) DEFAULT NULL,
  `dtCreaGp` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `GROUPE`
--

INSERT INTO `GROUPE` (`idGp`, `nomGp`, `dtCreaGp`) VALUES
(1, 'test', '1223-04-03'),
(2, 'Le monde', '2001-09-11');

-- --------------------------------------------------------

--
-- Structure de la table `LIKES`
--

CREATE TABLE `LIKES` (
  `eMailUser` varchar(50) NOT NULL,
  `idAlb` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `LIKES`
--

INSERT INTO `LIKES` (`eMailUser`, `idAlb`) VALUES
('afankous@gmail.com', 1),
('afantastik041@gmail.com', 1);

-- --------------------------------------------------------

--
-- Structure de la table `TITRE`
--

CREATE TABLE `TITRE` (
  `idTit` int NOT NULL,
  `idAlb` int NOT NULL,
  `nomTit` varchar(70) DEFAULT NULL,
  `dureeTit` float DEFAULT NULL,
  `audioTit` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `TITRE`
--

INSERT INTO `TITRE` (`idTit`, `idAlb`, `nomTit`, `dureeTit`, `audioTit`) VALUES
(1, 1, 'balance ton quoi', 43, NULL),
(2, 1, 'Je passe sous le bus', 168, 'uploads/audio/2bafef1b36c0cdda6f96ade3afb1395d.mp3'),
(3, 2, 'GOING OUT THE BLOCK', 27, 'uploads/audio/3b6bb30b6756e3ef57d4cb66c1b8e811.mp3'),
(4, 2, 'KLITTER', 17, 'uploads/audio/063b94685039973cb7d3f65c9aed5537.mp3'),
(5, 2, 'MC MARTIN', 37, 'uploads/audio/32d597702c88e8c8965fecef1a29fe05.mp3');

-- --------------------------------------------------------

--
-- Structure de la table `USER`
--

CREATE TABLE `USER` (
  `eMailUser` varchar(50) NOT NULL,
  `nomEUser` varchar(50) DEFAULT NULL,
  `prenomUser` varchar(50) DEFAULT NULL,
  `passwordHash` varchar(255) DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `USER`
--

INSERT INTO `USER` (`eMailUser`, `nomEUser`, `prenomUser`, `passwordHash`, `isAdmin`) VALUES
('afankous@gmail.com', 'afankous', 'mehdi', '$2y$10$ycFozPdW9Jas5W.mxPu6QeC5VfwRXgI0mymSuNaEEidNLhs23ulgu', 1),
('afantastik041@gmail.com', 'Afankous', 'mehdi', '$2y$10$PKwsr14GMLftAs7bqAhi9OppymYUTZijbGZ3lFQA.qAUrSBJ19b8C', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ALBUM`
--
ALTER TABLE `ALBUM`
  ADD PRIMARY KEY (`idAlb`),
  ADD KEY `ALBUM_FK` (`idAlb`),
  ADD KEY `FK_ASSOCIATION_0` (`idArt`),
  ADD KEY `FK_ASSOCIATION_2` (`idGp`);

--
-- Index pour la table `ARTISTE`
--
ALTER TABLE `ARTISTE`
  ADD PRIMARY KEY (`idArt`),
  ADD KEY `ARTISTE_FK` (`idArt`),
  ADD KEY `FK_ASSOCIATION_3` (`idGp`);

--
-- Index pour la table `AUTH_RATE_LIMIT`
--
ALTER TABLE `AUTH_RATE_LIMIT`
  ADD PRIMARY KEY (`bucket`);

--
-- Index pour la table `GROUPE`
--
ALTER TABLE `GROUPE`
  ADD PRIMARY KEY (`idGp`),
  ADD KEY `GROUPE_FK` (`idGp`);

--
-- Index pour la table `LIKES`
--
ALTER TABLE `LIKES`
  ADD PRIMARY KEY (`eMailUser`,`idAlb`),
  ADD KEY `LIKES_FK` (`eMailUser`),
  ADD KEY `LIKES2_FK` (`idAlb`);

--
-- Index pour la table `TITRE`
--
ALTER TABLE `TITRE`
  ADD PRIMARY KEY (`idTit`),
  ADD KEY `TITRE_FK` (`idTit`),
  ADD KEY `FK_ASSOCIATION_1` (`idAlb`);

--
-- Index pour la table `USER`
--
ALTER TABLE `USER`
  ADD PRIMARY KEY (`eMailUser`),
  ADD KEY `USER_FK` (`eMailUser`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `ALBUM`
--
ALTER TABLE `ALBUM`
  MODIFY `idAlb` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ARTISTE`
--
ALTER TABLE `ARTISTE`
  MODIFY `idArt` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `GROUPE`
--
ALTER TABLE `GROUPE`
  MODIFY `idGp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `TITRE`
--
ALTER TABLE `TITRE`
  MODIFY `idTit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ALBUM`
--
ALTER TABLE `ALBUM`
  ADD CONSTRAINT `FK_ASSOCIATION_0` FOREIGN KEY (`idArt`) REFERENCES `ARTISTE` (`idArt`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ASSOCIATION_2` FOREIGN KEY (`idGp`) REFERENCES `GROUPE` (`idGp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `ARTISTE`
--
ALTER TABLE `ARTISTE`
  ADD CONSTRAINT `FK_ASSOCIATION_3` FOREIGN KEY (`idGp`) REFERENCES `GROUPE` (`idGp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `LIKES`
--
ALTER TABLE `LIKES`
  ADD CONSTRAINT `FK_LIKES` FOREIGN KEY (`idAlb`) REFERENCES `ALBUM` (`idAlb`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_LIKES2` FOREIGN KEY (`eMailUser`) REFERENCES `USER` (`eMailUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `TITRE`
--
ALTER TABLE `TITRE`
  ADD CONSTRAINT `FK_ASSOCIATION_1` FOREIGN KEY (`idAlb`) REFERENCES `ALBUM` (`idAlb`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
