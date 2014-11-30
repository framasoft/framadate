-- --------------------------------------------------------

--
-- Table structure `poll`
--

CREATE TABLE IF NOT EXISTS `poll` (
  `id`              CHAR(16)  NOT NULL,
  `admin_id`        CHAR(24)  NOT NULL,
  `title`           TEXT      NOT NULL,
  `description`     TEXT,
  `admin_name`      VARCHAR(64) DEFAULT NULL,
  `admin_mail`      VARCHAR(128) DEFAULT NULL,
  `creation_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date`        TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `format`          VARCHAR(1) DEFAULT NULL,
  `editable`        TINYINT(1) DEFAULT '0',
  `receiveNewVotes` TINYINT(1) DEFAULT '0',
  `active`          TINYINT(1) DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- --------------------------------------------------------

--
-- Table structure `slot`
--

CREATE TABLE IF NOT EXISTS `slot` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `title`   TEXT,
  `moments` TEXT,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- --------------------------------------------------------

--
-- Table structure `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    TEXT,
  `comment` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- --------------------------------------------------------

--
-- Table structure `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    VARCHAR(64)      NOT NULL,
  `choices` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


--
-- Data for Name: poll; Type: TABLE DATA;
--

INSERT INTO `poll`
(`id`, `description`, `admin_mail`, `admin_name`, `title`, `admin_id`, `end_date`, `format`)
VALUES
  ('aqg259dth55iuhwm', 'Repas de Noel du service', 'Stephanie@retaillard.com', 'Stephanie', 'Repas de Noel',
   'aqg259dth55iuhwmy9d8jlwk', FROM_UNIXTIME('1627100361'), 'D');

--
-- Data for Name: slot; Type: TABLE DATA;
--

INSERT INTO `slot` (`poll_id`, `title`, `moments`) VALUES
  ('aqg259dth55iuhwm', '1225839600', '12h,19h'),
  ('aqg259dth55iuhwm', '1226012400', '12h,19h'),
  ('aqg259dth55iuhwm', '1226876400', '12h,19h'),
  ('aqg259dth55iuhwm', '1227826800', '12h,19h');

--
-- Data for Name: vote; Type: TABLE DATA;
--

INSERT INTO `vote` (`name`, `poll_id`, `choices`) VALUES
  ('marcel', 'aqg259dth55iuhwm', '02202222'),
  ('paul', 'aqg259dth55iuhwm', '20220202'),
  ('sophie', 'aqg259dth55iuhwm', '22202200'),
  ('barack', 'aqg259dth55iuhwm', '02200000'),
  ('takashi', 'aqg259dth55iuhwm', '00002202'),
  ('albert', 'aqg259dth55iuhwm', '20202200'),
  ('alfred', 'aqg259dth55iuhwm', '02200200'),
  ('marcs', 'aqg259dth55iuhwm', '02000020'),
  ('laure', 'aqg259dth55iuhwm', '00220000'),
  ('benda', 'aqg259dth55iuhwm', '22022022'),
  ('albert', 'aqg259dth55iuhwm', '22222200');
