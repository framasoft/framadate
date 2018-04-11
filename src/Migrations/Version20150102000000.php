<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Framadate\Utils;

/**
 * This class executes the aciton in database to migrate data from version 0.8 to 0.9.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20150102000000 extends AbstractMigration
{

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'From 0.8 to 0.9';
    }

    private function createPollTable()
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS `' . Utils::table('poll') . '` (
  `id`              CHAR(16)  NOT NULL,
  `admin_id`        CHAR(24)  NOT NULL,
  `title`           TEXT      NOT NULL,
  `description`     TEXT,
  `admin_name`      VARCHAR(64) DEFAULT NULL,
  `admin_mail`      VARCHAR(128) DEFAULT NULL,
  `creation_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date`        TIMESTAMP NULL DEFAULT NULL,
  `format`          VARCHAR(1) DEFAULT NULL,
  `editable`        TINYINT(1) DEFAULT \'0\',
  `receiveNewVotes` TINYINT(1) DEFAULT \'0\',
  `active`          TINYINT(1) DEFAULT \'1\',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8');
    }

    private function migrateFromSondageToPoll()
    {
        $select = $this->connection->query('
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

        $insert = $this->connection->prepare('
INSERT INTO `' . Utils::table('poll') . '`
(`id`, `admin_id`, `title`, `description`, `admin_name`, `admin_mail`, `creation_date`, `end_date`, `format`, `editable`, `receiveNewVotes`, `active`)
VALUE (?,?,?,?,?,?,?,?,?,?,?,?)');

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                $row->id_sondage,
                $row->id_sondage_admin,
                $this->unescape($row->titre),
                $this->unescape($row->commentaires),
                $this->unescape($row->nom_admin),
                $this->unescape($row->mail_admin),
                $row->date_creation,
                $row->date_fin,
                $row->format,
                $row->editable,
                $row->mailsonde,
                $row->active
            ]);
        }
    }

    private function createSlotTable()
    {
        $this->addSql('
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

    private function migrateFromSujetStudsToSlot()
    {
        $stmt = $this->connection->query('SELECT * FROM sujet_studs');
        $sujets = $stmt->fetchAll();
        $slots = [];

        foreach ($sujets as $sujet) {
            $newSlots = $this->transformSujetToSlot($sujet);
            foreach ($newSlots as $newSlot) {
                $slots[] = $newSlot;
            }
        }

        $prepared = $this->connection->prepare('INSERT INTO ' . Utils::table('slot') . ' (`poll_id`, `title`, `moments`) VALUE (?,?,?)');
        foreach ($slots as $slot) {
            $prepared->execute([
                $slot->poll_id,
                $this->unescape($slot->title),
                !empty($slot->moments) ? $this->unescape($slot->moments) : null
            ]);
        }
    }

    private function createCommentTable()
    {
        $this->addSql('
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

    private function migrateFromCommentsToComment()
    {
        $select = $this->connection->query('
SELECT
    `id_sondage`,
    `usercomment`,
    `comment`
  FROM `comments`');

        $insert = $this->connection->prepare('
INSERT INTO `' . Utils::table('comment') . '` (`poll_id`, `name`, `comment`)
VALUE (?,?,?)');

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                $row->id_sondage,
                $this->unescape($row->usercomment),
                $this->unescape($row->comment)
            ]);
        }
    }

    private function createVoteTable()
    {
        $this->addSql('
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

    private function migrateFromUserStudsToVote()
    {
        $select = $this->connection->query('
SELECT
    `id_sondage`,
    `nom`,
    REPLACE(REPLACE(REPLACE(`reponses`, 1, \'X\'), 2, 1), \'X\', 2) reponses
  FROM `user_studs`');

        $insert = $this->connection->prepare('
INSERT INTO `' . Utils::table('vote') . '` (`poll_id`, `name`, `choices`)
VALUE (?,?,?)');

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                                 $row->id_sondage,
                                 $this->unescape($row->nom),
                                 $row->reponses
                             ]);
        }
    }

    private function transformSujetToSlot($sujet)
    {
        $slots = [];
        $ex = explode(',', $sujet->sujet);
        $isDatePoll = strpos($sujet->sujet, '@');
        $lastSlot = null;

        foreach ($ex as $atomicSlot) {
            if ($isDatePoll === false) { // Classic poll
                $slot = new \stdClass();
                $slot->poll_id = $sujet->id_sondage;
                $slot->title = $atomicSlot;
                $slots[] = $slot;
            } else { // Date poll
                $values = explode('@', $atomicSlot);
                if ($lastSlot === null || $lastSlot->title !== $values[0]) {
                    $lastSlot = new \stdClass();
                    $lastSlot->poll_id = $sujet->id_sondage;
                    $lastSlot->title = $values[0];
                    $lastSlot->moments = count($values) === 2 ? $values[1] : '-';
                    $slots[] = $lastSlot;
                } else {
                    $lastSlot->moments .= ',' . (count($values) === 2 ? $values[1] : '-');
                }
            }
        }

        return $slots;
    }

    private function dropOldTables()
    {
        $this->addSql('DROP TABLE `comments`');
        $this->addSql('DROP TABLE `sujet_studs`');
        $this->addSql('DROP TABLE `user_studs`');
        $this->addSql('DROP TABLE `sondage`');
    }

    private function unescape($value)
    {
        return stripslashes(html_entity_decode($value, ENT_QUOTES));
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     */
    public function up(Schema $schema)
    {
        foreach (['sondage', 'sujet_studs', 'comments', 'user_studs'] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }

        $this->createPollTable();
        $this->createCommentTable();
        $this->createSlotTable();
        $this->createVoteTable();

        $this->migrateFromSondageToPoll();
        $this->migrateFromCommentsToComment();
        $this->migrateFromSujetStudsToSlot();
        $this->migrateFromUserStudsToVote();

        $this->dropOldTables();
    }

    public function down(Schema $schema)
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS `sondage` (
  `id_sondage` char(16) NOT NULL,
  `commentaires` text,
  `mail_admin` varchar(128) DEFAULT NULL,
  `nom_admin` varchar(64) DEFAULT NULL,
  `titre` text,
  `id_sondage_admin` char(24) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_fin` timestamp NOT NULL,
  `format` varchar(2) DEFAULT NULL,
  `mailsonde` tinyint(1) DEFAULT \'0\',
  `statut` int(11) NOT NULL DEFAULT \'1\' COMMENT \'1 = actif ; 0 = inactif ; \',
  UNIQUE KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $this->addSql('
CREATE TABLE IF NOT EXISTS `sujet_studs` (
  `id_sondage` char(16) NOT NULL,
  `sujet` text,
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $this->addSql('
CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_sondage` char(16) NOT NULL,
  `comment` text NOT NULL,
  `usercomment` text,
  PRIMARY KEY (`id_comment`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;');

        $this->addSql('
CREATE TABLE IF NOT EXISTS `user_studs` (
  `id_users` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `id_sondage` char(16) NOT NULL,
  `reponses` text NOT NULL,
  PRIMARY KEY (`id_users`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;');

        $schema->dropTable(Utils::table('poll'));
        $schema->dropTable(Utils::table('comment'));
        $schema->dropTable(Utils::table('vote'));
        $schema->dropTable(Utils::table('slot'));
    }
}
