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

-- --------------------------------------------------------

--
-- Migrate data from `sondage` to `poll`
--

INSERT INTO `poll`
(`id`, `admin_id`, `title`, `description`, `admin_name`, `admin_mail`, `creation_date`, `end_date`, `format`, `editable`, `receiveNewVotes`, `active`)
  SELECT
    `id_sondage`,
    `id_sondage_admin`,
    `titre`,
    `commentaires`,
    `nom_admin`,
    `mail_admin`,
    `titre`,
    `date_creation`,
    `date_fin`,
    SUBSTR(`format`, 1, 1) AS `format`,
    CASE SUBSTR(`format`, 2, 1)
    WHEN '+' THEN 1
    ELSE 0 END             AS `editable`,
    `mailsonde`,
    CASE SUBSTR(`format`, 2, 1)
    WHEN '-' THEN 0
    ELSE 1 END             AS `active`
  FROM sondage;

-- --------------------------------------------------------

--
-- Migrate data from `sujet_studs` to `slot`
--

-- TODO Migrate this, is not so simple
/*INSERT INTO `slot`
(`poll_id`, `title`, `moments`)
    SELECT `id_sondage`,
      FROM `user_studs`;*/

-- --------------------------------------------------------

--
-- Migrate data from `comments` to `comment`
--

INSERT INTO `comment`
(`poll_id`, `name`, `comment`)
  SELECT `id_sondage`, `usercomment`, `comment`
  FROM `comments`;

-- --------------------------------------------------------

--
-- Migrate data from `user_studs` to `vote`
--

INSERT INTO `vote`
(`poll_id`, `name`, `choices`)
  SELECT `id_sondage`, `nom`, REPLACE(REPLACE(REPLACE(`reponses`, '1', 'X'), '2', '1'), 'X', 2)
  FROM `user_studs`;
