# tiktok-account-video-informations-saver

Migration :

```sql
-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  mar. 29 déc. 2020 à 14:48
-- Version du serveur :  5.7.17
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `channel_storage`
--

-- --------------------------------------------------------

--
-- Structure de la table `tiktok_account`
--

CREATE TABLE `tiktok_account` (
  `id` int(11) NOT NULL,
  `tiktok_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL,
  `api_url` text NOT NULL COMMENT 'Example: https://m.tiktok.com/api/post/item_list/?aid=1988&count=30&cursor=now&secUid=MS4wLjABAAAA7xnwUIcPFptmOrop7D8ycz5abNlPN1C9dKGDNX296krcgjGr-ePOIyksaH4Bi2Nn',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
-- --------------------------------------------------------

--
-- Structure de la table `tiktok_video`
--

CREATE TABLE `tiktok_video` (
  `id` int(11) NOT NULL,
  `account_id` INT NOT NULL,
  `tiktok_id` varchar(255) NOT NULL,
  `tiktok_url` varchar(255) NOT NULL,
  `legend` text NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
ALTER TABLE `tiktok_video` CHANGE `legend` `legend` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

--
-- Index pour la table `tiktok_account`
--
ALTER TABLE `tiktok_account`
  ADD PRIMARY KEY (`id`);
--
-- Index pour la table `tiktok_video`
--
ALTER TABLE `tiktok_video`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `tiktok_account`
--
ALTER TABLE `tiktok_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tiktok_video`
--
ALTER TABLE `tiktok_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

```
