-- Base de données: `opensondage`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_sondage` char(16) NOT NULL,
  `comment` text NOT NULL,
  `usercomment` text,
  PRIMARY KEY (`id_comment`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `sondage`
--

CREATE TABLE IF NOT EXISTS `sondage` (
  `id_sondage` char(16) NOT NULL,
  `commentaires` text,
  `mail_admin` varchar(128) DEFAULT NULL,
  `nom_admin` varchar(64) DEFAULT NULL,
  `titre` text,
  `id_sondage_admin` char(24) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_fin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `format` varchar(2) DEFAULT NULL,
  `mailsonde` tinyint(1) DEFAULT '0',
  `statut` int(11) NOT NULL DEFAULT '1' COMMENT '1 = actif ; 0 = inactif ; ',
  UNIQUE KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `sujet_studs`
--

CREATE TABLE IF NOT EXISTS `sujet_studs` (
  `id_sondage` char(16) NOT NULL,
  `sujet` text,
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `user_studs`
--

CREATE TABLE IF NOT EXISTS `user_studs` (
  `id_users` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `id_sondage` char(16) NOT NULL,
  `reponses` text NOT NULL,
  PRIMARY KEY (`id_users`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=160284 ;



INSERT INTO `sondage`
(`id_sondage`, `commentaires`, `mail_admin`, `nom_admin`,
	     `titre`, `id_sondage_admin`,
	     `date_fin`, `format`)
VALUES
('aqg259dth55iuhwm','Repas de Noel du service','Stephanie@retaillard.com','Stephanie',
			   'Repas de Noel','aqg259dth55iuhwmy9d8jlwk',
			   FROM_UNIXTIME('1627100361'),'D+');

--
-- Data for Name: sujet_studs; Type: TABLE DATA;
--

INSERT INTO `sujet_studs` (`id_sondage`, `sujet`) VALUES
('aqg259dth55iuhwm','1225839600@12h,1225839600@19h,1226012400@12h,1226012400@19h,1226876400@12h,1226876400@19h,1227049200@12h,1227049200@19h,1227826800@12h,1227826800@19h');

--
-- Data for Name: user_studs; Type: TABLE DATA;
--

INSERT INTO `user_studs` (`nom`, `id_sondage`, `reponses`, `id_users`) VALUES
('marcel','aqg259dth55iuhwm','0110111101','933'),
('paul','aqg259dth55iuhwm','1011010111','935'),
('sophie','aqg259dth55iuhwm','1110110000','945'),
('barack','aqg259dth55iuhwm','0110000','948'),
('takashi','aqg259dth55iuhwm','0000110100','951'),
('albert','aqg259dth55iuhwm','1010110','975'),
('alfred','aqg259dth55iuhwm','0110010','1135'),
('marcs','aqg259dth55iuhwm','0100001010','1143'),
('laure','aqg259dth55iuhwm','0011000','1347'),
('benda','aqg259dth55iuhwm','1101101100','1667'),
('Albert','aqg259dth55iuhwm','1111110011','1668');
