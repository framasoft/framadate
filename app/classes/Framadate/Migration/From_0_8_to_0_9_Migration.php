<?php
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * This class executes the aciton in database to migrate data from version 0.8 to 0.9.
 *
 * @package Framadate\Migration
 */
class From_0_8_to_0_9_Migration implements Migration {

    function __construct() {
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $this->createPollTable($pdo);
        $this->migrateFromSondageToPoll($pdo);

        $this->createSlotTable($pdo);
        $this->migrateFromSujetStudsToSlot($pdo);

        $this->createCommentTable($pdo);
        $this->migrateFromCommentsToComment($pdo);

        $this->createVoteTable($pdo);
        $this->migrateFromUserStudsToVote($pdo);

        $this->dropOldTables($pdo);

        return true;
    }

    private function createPollTable(\PDO $pdo) {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . Utils::table('poll') . '` (
  `id`              CHAR(16)  NOT NULL,
  `admin_id`        CHAR(24)  NOT NULL,
  `title`           TEXT      NOT NULL,
  `description`     TEXT,
  `admin_name`      VARCHAR(64) DEFAULT NULL,
  `admin_mail`      VARCHAR(128) DEFAULT NULL,
  `creation_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date`        TIMESTAMP NOT NULL DEFAULT \'0000-00-00 00:00:00\',
  `format`          VARCHAR(1) DEFAULT NULL,
  `editable`        TINYINT(1) DEFAULT \'0\',
  `receiveNewVotes` TINYINT(1) DEFAULT \'0\',
  `active`          TINYINT(1) DEFAULT \'1\',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8');
    }

    private function migrateFromSondageToPoll(\PDO $pdo) {
        $pdo->exec('
INSERT INTO `' . Utils::table('poll') . '`
(`id`, `admin_id`, `title`, `description`, `admin_name`, `admin_mail`, `creation_date`, `end_date`, `format`, `editable`, `receiveNewVotes`, `active`)
  SELECT
    `id_sondage`,
    `id_sondage_admin`,
    `titre`,
    `commentaires`,
    `nom_admin`,
    `mail_admin`,
    `date_creation`,
    `date_fin`,
    SUBSTR(`format`, 1, 1) AS `format`,
    CASE SUBSTR(`format`, 2, 1)
    WHEN \'+\' THEN 1
    ELSE 0 END             AS `editable`,
    `mailsonde`,
    CASE SUBSTR(`format`, 2, 1)
    WHEN \'-\' THEN 0
    ELSE 1 END             AS `active`
  FROM sondage');
    }

    private function createSlotTable(\PDO $pdo) {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . Utils::table('slot') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `title`   TEXT,
  `moments` TEXT,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8');
    }

    private function migrateFromSujetStudsToSlot(\PDO $pdo) {
        $stmt = $pdo->query('SELECT * FROM sujet_studs');
        $sujets = $stmt->fetchAll();
        $slots = [];

        foreach ($sujets as $sujet) {
            $newSlots = $this->transformSujetToSlot($sujet);
            $slots = array_merge($slots, $newSlots);
        }

        $prepared = $pdo->prepare('INSERT INTO ' . Utils::table('slot') . ' (`poll_id`, `title`, `moments`) VALUE (?,?,?)');
        foreach ($slots as $slot) {
            $prepared->execute([$slot->poll_id, $slot->title, $slot->moments]);
        }
    }

    private function createCommentTable(\PDO $pdo) {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . Utils::table('comment') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    TEXT,
  `comment` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8');
    }

    private function migrateFromCommentsToComment(\PDO $pdo) {
        $pdo->exec('
INSERT INTO `' . Utils::table('comment') . '`
(`poll_id`, `name`, `comment`)
  SELECT
    `id_sondage`,
    `usercomment`,
    `comment`
  FROM `comments`');
    }

    private function createVoteTable(\PDO $pdo) {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . Utils::table('vote') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    VARCHAR(64)      NOT NULL,
  `choices` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8');
    }

    private function migrateFromUserStudsToVote(\PDO $pdo) {
        $pdo->exec('
INSERT INTO `' . Utils::table('vote') . '`
(`poll_id`, `name`, `choices`)
  SELECT
    `id_sondage`,
    `nom`,
    REPLACE(REPLACE(REPLACE(`reponses`, 1, \'X\'), 2, 1), \'X\', 2)
  FROM `user_studs`');
    }

    private function transformSujetToSlot($sujet) {
        $slots = [];
        $ex = explode(',', $sujet->sujet);
        $lastSlot = null;

        foreach ($ex as $atomicSlot) {
            $values = explode('@', $atomicSlot);
            if ($lastSlot == null || $lastSlot->title !== $values[0]) {
                $lastSlot = new \stdClass();
                $lastSlot->poll_id = $sujet->id_sondage;
                $lastSlot->title = $values[0];
                $lastSlot->moments = count($values) == 2 ? $values[1] : null;
                $slots[] = $lastSlot;
            } else {
                $lastSlot->moments .= ',' . $values[1];
            }
        }

        return $slots;
    }

    private function dropOldTables(\PDO $pdo) {
        $pdo->exec('DROP TABLE `comments`');
        $pdo->exec('DROP TABLE `sujet_studs`');
        $pdo->exec('DROP TABLE `user_studs`');
        $pdo->exec('DROP TABLE `sondage`');
    }

}
 