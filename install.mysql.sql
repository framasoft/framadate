CREATE TABLE IF NOT EXISTS `sondage` (
       `id_sondage` CHAR(16) NOT NULL,
       `commentaires` text,
       `mail_admin` VARCHAR(128),
       `nom_admin` VARCHAR(64),
       `titre` text,
       `id_sondage_admin` CHAR(24),
       `date_fin` TIMESTAMP,
       `format` VARCHAR(2),
       `mailsonde` BOOLEAN DEFAULT '0',
       		   UNIQUE KEY (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Name: sujet_studs; Type: TABLE;
--

CREATE TABLE IF NOT EXISTS `sujet_studs` (
    `id_sondage` CHAR(16) NOT NULL,
    `sujet` TEXT,
		    FOREIGN KEY (`id_sondage`) REFERENCES sondage(id_sondage) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Name: user_studs; Type: TABLE;
--

CREATE TABLE IF NOT EXISTS `user_studs` (
    `id_users` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `nom` VARCHAR(64) NOT NULL,
    `id_sondage` CHAR(16) NOT NULL,
    `reponses` text NOT NULL,
    	     PRIMARY KEY (`id_users`),
	     FOREIGN KEY (`id_sondage`) REFERENCES sondage(id_sondage) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Name: comments; Type: TABLE;
--

CREATE TABLE IF NOT EXISTS `comments` (
    `id_comment` INT(11) unsigned NOT NULL AUTO_INCREMENT,
    `id_sondage` CHAR(16) NOT NULL,
    `comment` text NOT NULL,
    `usercomment` text,
    	     PRIMARY KEY (`id_comment`),
	     FOREIGN KEY (`id_sondage`) REFERENCES sondage(id_sondage) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Data for Name: sondage; Type: TABLE DATA;
--

INSERT INTO `sondage`
(`id_sondage`, `commentaires`, `mail_admin`, `nom_admin`,
	     `titre`, `id_sondage_admin`,
	     `date_fin`, `format`)
VALUES
('aqg259dth55iuhwm','Repas de Noel du service','Stephanie@saillard.com','Stephanie',
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
