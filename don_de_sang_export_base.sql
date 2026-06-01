-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 27 nov. 2025 à 23:47
-- Version du serveur : 8.0.44-0ubuntu0.22.04.1
-- Version de PHP : 8.1.2-1ubuntu2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `don_de_sang`
--

-- --------------------------------------------------------

--
-- Structure de la table `besoins`
--

CREATE TABLE `besoins` (
  `id_besoin` int NOT NULL,
  `groupe_sanguin` enum('A','B','O','AB') COLLATE utf8mb3_unicode_ci NOT NULL,
  `niveau_alerte` enum('URGENT','CRITIQUE','NORMAL') COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `centres_collecte`
--

CREATE TABLE `centres_collecte` (
  `id_centre` int NOT NULL,
  `nom_centre` varchar(80) COLLATE utf8mb3_unicode_ci NOT NULL,
  `ville` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `centres_collecte`
--

INSERT INTO `centres_collecte` (`id_centre`, `nom_centre`, `ville`) VALUES
(1, 'sousse_clinique', 'Sousse');

-- --------------------------------------------------------

--
-- Structure de la table `donneurs`
--

CREATE TABLE `donneurs` (
  `id_donneur` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `cin` int NOT NULL,
  `date_naissance` date NOT NULL,
  `ville` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `groupe_sanguin` enum('A','B','O','AB') COLLATE utf8mb3_unicode_ci NOT NULL,
  `rhesus` enum('+','-') COLLATE utf8mb3_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dons`
--

CREATE TABLE `dons` (
  `id_don` int NOT NULL,
  `id_donneur` int NOT NULL,
  `id_centre` int NOT NULL,
  `statut` enum('EN STOCK','UTILISE','REJETE') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_don` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tests_don`
--

CREATE TABLE `tests_don` (
  `id_test` int NOT NULL,
  `id_don` int NOT NULL,
  `est_conforme` tinyint(1) DEFAULT '0' COMMENT '1=conforme,0=rejeté'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transfusions`
--

CREATE TABLE `transfusions` (
  `id_transfusion` int NOT NULL,
  `id_don` int NOT NULL,
  `hopital_recepteur` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_transfusion` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int NOT NULL,
  `nom_utilisateur` varchar(50) COLLATE utf8mb3_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `role` enum('ADMIN','MEDECIN','SECRETAIRE') COLLATE utf8mb3_unicode_ci NOT NULL,
  `id_centre` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom_utilisateur`, `mot_de_passe`, `role`, `id_centre`) VALUES
(1, 'Eya', '$2y$10$tMpUULOVQapaWy/AccSUTuPryLAiWHiyj0YL4egXyuhm4bnuwW3te', 'ADMIN', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `besoins`
--
ALTER TABLE `besoins`
  ADD PRIMARY KEY (`id_besoin`);

--
-- Index pour la table `centres_collecte`
--
ALTER TABLE `centres_collecte`
  ADD PRIMARY KEY (`id_centre`);

--
-- Index pour la table `donneurs`
--
ALTER TABLE `donneurs`
  ADD PRIMARY KEY (`id_donneur`),
  ADD UNIQUE KEY `UNIQUE` (`cin`) USING BTREE;

--
-- Index pour la table `dons`
--
ALTER TABLE `dons`
  ADD PRIMARY KEY (`id_don`),
  ADD KEY `FK_id_centre` (`id_centre`) USING BTREE,
  ADD KEY `FK_id_donneur` (`id_donneur`);

--
-- Index pour la table `tests_don`
--
ALTER TABLE `tests_don`
  ADD PRIMARY KEY (`id_test`),
  ADD UNIQUE KEY `id_don` (`id_don`) USING BTREE,
  ADD KEY `FK_id_don` (`id_don`) USING BTREE;

--
-- Index pour la table `transfusions`
--
ALTER TABLE `transfusions`
  ADD PRIMARY KEY (`id_transfusion`),
  ADD UNIQUE KEY `id_don` (`id_don`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `nom_utilisateur` (`nom_utilisateur`) USING BTREE,
  ADD KEY `FK_idcentre` (`id_centre`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `besoins`
--
ALTER TABLE `besoins`
  MODIFY `id_besoin` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `centres_collecte`
--
ALTER TABLE `centres_collecte`
  MODIFY `id_centre` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `donneurs`
--
ALTER TABLE `donneurs`
  MODIFY `id_donneur` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dons`
--
ALTER TABLE `dons`
  MODIFY `id_don` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tests_don`
--
ALTER TABLE `tests_don`
  MODIFY `id_test` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transfusions`
--
ALTER TABLE `transfusions`
  MODIFY `id_transfusion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `dons`
--
ALTER TABLE `dons`
  ADD CONSTRAINT `fk_dons_centre` FOREIGN KEY (`id_centre`) REFERENCES `centres_collecte` (`id_centre`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_id_donneur` FOREIGN KEY (`id_donneur`) REFERENCES `donneurs` (`id_donneur`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `tests_don`
--
ALTER TABLE `tests_don`
  ADD CONSTRAINT `FK_id_don` FOREIGN KEY (`id_don`) REFERENCES `dons` (`id_don`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `FK_idcentre` FOREIGN KEY (`id_centre`) REFERENCES `centres_collecte` (`id_centre`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
