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
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * This class executes the acton in database to migrate data from version 0.8 to 0.9.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class From_0_8_to_0_9_Migration implements Migration {

    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return 'From 0.8 to 0.9';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
	switch(DB_DRIVER_NAME){
		case "mysql":
			$stmt = $pdo->query('SHOW TABLES');
			break;
		case "pgsql":
			$stmt = $pdo->query('SELECT tablename FROM pg_tables WHERE tablename !~ \'^pg_\' AND tablename !~ \'^sql_\';');
			break;
	}
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if tables of v0.8 are presents
        $diff = array_diff(['sondage', 'sujet_studs', 'comments', 'user_studs'], $tables);
        return count($diff) === 0;
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $this->createPollTable($pdo);
        $this->createCommentTable($pdo);
        $this->createSlotTable($pdo);
        $this->createVoteTable($pdo);

        $pdo->beginTransaction();
        $this->migrateFromSondageToPoll($pdo);
        $this->migrateFromCommentsToComment($pdo);
        $this->migrateFromSujetStudsToSlot($pdo);
        $this->migrateFromUserStudsToVote($pdo);
        $pdo->commit();

        $this->dropOldTables($pdo);

        return true;
    }

    private function createPollTable(\PDO $pdo) {
	$query_create_table_poll = null;
	switch(DB_DRIVER_NAME){
	   case 'mysql':
		$query_create_table_poll = '
CREATE TABLE IF NOT EXISTS `' . Utils::table('poll') . '` (
  `id`              CHAR(16)  NOT NULL,
  `admin_id`        CHAR(24)  NOT NULL,
  `title`           TEXT      NOT NULL,
  `description`     TEXT,
  `admin_name`      VARCHAR(64) DEFAULT NULL,
  `admin_mail`      VARCHAR(128) DEFAULT NULL,
  `creation_date`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_date`        TIMESTAMP NOT NULL,
  `format`          VARCHAR(1) DEFAULT NULL,
  `editable`        TINYINT(1) DEFAULT \'0\',
  `receiveNewVotes` TINYINT(1) DEFAULT \'0\',
  `active`          TINYINT(1) DEFAULT \'1\',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8';
		break;

	  case 'pgsql':
		$query_create_table_poll = '
CREATE TABLE IF NOT EXISTS ' . Utils::table('poll') . ' (
  id              CHAR(16)  NOT NULL PRIMARY KEY,
  admin_id        CHAR(24)  NOT NULL,
  title           TEXT      NOT NULL,
  description     TEXT,
  admin_name      VARCHAR(64) DEFAULT NULL,
  admin_mail      VARCHAR(128) DEFAULT NULL,
  creation_date   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  end_date        TIMESTAMP NOT NULL DEFAULT \'epoch\',
  format          VARCHAR(1) DEFAULT NULL,
  editable        SMALLINT DEFAULT \'0\',
  receiveNewVotes SMALLINT DEFAULT \'0\',
  active          SMALLINT DEFAULT \'1\'
) ';
               break;
       }
       $pdo->exec($query_create_table_poll);
    }

    private function migrateFromSondageToPoll(\PDO $pdo) {
	$query_select_table_sondage = null;
	switch(DB_DRIVER_NAME){
	   case 'mysql':
		$query_select_table_sondage ='
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
  FROM sondage';
		$query_insert_table_poll = '
INSERT INTO `' . Utils::table('poll') . '`
(`id`, `admin_id`, `title`, `description`, `admin_name`, `admin_mail`, `creation_date`, `end_date`, `format`, `editable`, `receiveNewVotes`, `active`)
VALUE (?,?,?,?,?,?,?,?,?,?,?,?)';
                break;

	  case 'pgsql':
                $query_select_table_sondage ='
SELECT
    id_sondage,
    id_sondage_admin,
    titre,
    commentaires,
    nom_admin,
    mail_admin,
    date_creation,
    date_fin,
    SUBSTR(format, 1, 1) AS format,
    CASE SUBSTR(format, 2, 1)
    WHEN \'+\' THEN 1
    ELSE 0 END             AS editable,
    mailsonde,
    CASE SUBSTR(format, 2, 1)
    WHEN \'-\' THEN 0
    ELSE 1 END             AS active
  FROM sondage';

		$query_insert_table_poll = '
INSERT INTO ' . Utils::table('poll') . '
(id, admin_id, title, description, admin_name, admin_mail, creation_date, end_date, format, editable, receiveNewVotes, active)
VALUE (?,?,?,?,?,?,?,?,?,?,?,?)';
	        break;
	}

        $select = $pdo->query($query_select_table_sondage);
        $insert = $pdo->prepare($query_insert_table_poll);

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

    private function createSlotTable(\PDO $pdo) {
	$query_create_table_slot = null;
	switch(DB_DRIVER_NAME){
	   case 'mysql':
		$query_create_table_slot = '
CREATE TABLE IF NOT EXISTS `' . Utils::table('slot') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `title`   TEXT,
  `moments` TEXT,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8';
		break;
	   case 'pgsql':
		   $query_create_table_slot = '
CREATE TABLE IF NOT EXISTS ' . Utils::table('slot') . ' (
  id      BIGSERIAL        NOT NULL PRIMARY KEY,
  poll_id CHAR(16)         NOT NULL,
  title   TEXT,
  moments TEXT
);
CREATE INDEX slot_poll_id_index ON ' . Utils::table('slot') .' (poll_id);';
		break;
	}
	$pdo->exec($query_create_table_slot);
    }

    private function migrateFromSujetStudsToSlot(\PDO $pdo) {
        $stmt = $pdo->query('SELECT * FROM sujet_studs');
        $sujets = $stmt->fetchAll();
        $slots = [];

        foreach ($sujets as $sujet) {
            $newSlots = $this->transformSujetToSlot($sujet);
            foreach ($newSlots as $newSlot) {
                $slots[] = $newSlot;
            }
        }
	$query_insert_table_slot = null;
	switch(DB_DRIVER_NAME){
		case 'mysql':
	             $query_insert_table_slot = 'INSERT INTO ' . Utils::table('slot') . ' (`poll_id`, `title`, `moments`) VALUE (?,?,?)';
		     break;
		case 'pgsql':
	             $query_insert_table_slot = 'INSERT INTO ' . Utils::table('slot') . ' (poll_id, title, moments) VALUE (?,?,?)';
		     break;
	}

        $prepared = $pdo->prepare($query_insert_table_slot);
        foreach ($slots as $slot) {
            $prepared->execute([
                $slot->poll_id,
                $this->unescape($slot->title),
                !empty($slot->moments) ? $this->unescape($slot->moments) : null
            ]);
        }
    }

    private function createCommentTable(\PDO $pdo) {
	$query_create_table_comment = null;
	switch(DB_DRIVER_NAME){
		case 'mysql':
			$query_create_table_comment = '
CREATE TABLE IF NOT EXISTS `' . Utils::table('comment') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    TEXT,
  `comment` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8';
			break;
		case 'pgsql':
			$query_create_table_comment = '
CREATE TABLE IF NOT EXISTS ' . Utils::table('comment') . ' (
  id      BIGSERIAL        NOT NULL PRIMARY KEY,
  poll_id CHAR(16)         NOT NULL,
  name    TEXT,
  comment TEXT             NOT NULL
);
CREATE INDEX comment_poll_id_index ON ' . Utils::table('comment') .' (poll_id);';
	}
        $pdo->exec($query_create_table_comment);
    }

    private function migrateFromCommentsToComment(\PDO $pdo) {
	$query_select_table_comments = null;
	switch(DB_DRIVER_NAME){
		case 'mysql':
			$query_select_table_comments = '
SELECT
    `id_sondage`,
    `usercomment`,
    `comment`
  FROM `comments`';
			$query_insert_table_comment = '
INSERT INTO `' . Utils::table('comment') . '` (`poll_id`, `name`, `comment`) VALUE (?,?,?)';
			break;
		case 'pgsql':
			$query_select_table_comments = '
SELECT
    id_sondage,
    usercomment,
    comment
  FROM comments';
			$query_insert_table_comment = '
INSERT INTO ' . Utils::table('comment') . ' (poll_id, name, comment) VALUE (?,?,?)';
			break;
	}
        $select = $pdo->query($query_select_table_comments);
        $insert = $pdo->prepare($query_insert_table_comment);

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                $row->id_sondage,
                $this->unescape($row->usercomment),
                $this->unescape($row->comment)
            ]);
        }
    }

    private function createVoteTable(\PDO $pdo) {
	    $query_create_table_vote = null;
	    switch(DB_DRIVER_NAME){
                case 'mysql':
		    $query_create_table_vote = '
CREATE TABLE IF NOT EXISTS `' . Utils::table('vote') . '` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` CHAR(16)         NOT NULL,
  `name`    VARCHAR(64)      NOT NULL,
  `choices` TEXT             NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8';
		    break;
	       case 'pgsql':
		    $query_create_table_vote = '
CREATE TABLE IF NOT EXISTS ' . Utils::table('vote') . ' (
  id      BIGSERIAL        NOT NULL PRIMARY KEY,
  poll_id CHAR(16)         NOT NULL,
  name    VARCHAR(64)      NOT NULL,
  choices TEXT             NOT NULL
);
CREATE INDEX vote_poll_id_index ON ' . Utils::table('vote') .' (poll_id)';
		     break;
	   }
	   $pdo->exec($query_create_table_vote);
    }

    private function migrateFromUserStudsToVote(\PDO $pdo) {
	$query_select_table_user_studs = null;
	switch(DB_DRIVER_NAME){
	     case 'mysql':
		$query_select_table_user_studs = '
SELECT
    `id_sondage`,
    `nom`,
    REPLACE(REPLACE(REPLACE(`reponses`, 1, \'X\'), 2, 1), \'X\', 2) reponses
  FROM `user_studs`';
		$query_insert_table_vote = '
INSERT INTO `' . Utils::table('vote') . '` (`poll_id`, `name`, `choices`) VALUE (?,?,?)';
		break;

	     case 'pgsql':
		$query_select_table_user_studs = '
SELECT
    id_sondage,
    nom,
    REPLACE(REPLACE(REPLACE(reponses, \'1\', \'X\'), \'2\', \'1\'), \'X\', \'2\') reponses
  FROM user_studs';
		$query_insert_table_vote = '
INSERT INTO ' . Utils::table('vote') . ' (poll_id, name, choices) VALUE (?,?,?)';
		break;
	}
	    
        $select = $pdo->query($query_select_table_user_studs);
        $insert = $pdo->prepare($query_insert_table_vote);

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                                 $row->id_sondage,
                                 $this->unescape($row->nom),
                                 $row->reponses
                             ]);
        }
    }

    private function transformSujetToSlot($sujet) {
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
                if ($lastSlot == null || $lastSlot->title !== $values[0]) {
                    $lastSlot = new \stdClass();
                    $lastSlot->poll_id = $sujet->id_sondage;
                    $lastSlot->title = $values[0];
                    $lastSlot->moments = count($values) == 2 ? $values[1] : '-';
                    $slots[] = $lastSlot;
                } else {
                    $lastSlot->moments .= ',' . (count($values) == 2 ? $values[1] : '-');
                }
            }
        }

        return $slots;
    }

    private function dropOldTables(\PDO $pdo) {
	$query_drop_old_tables = null;
	switch (DB_DRIVER_NAME) {
		case 'mysql':
			$query_drop_old_tables = '
DROP TABLE `comments`;
DROP TABLE `sujet_studs`;
DROP TABLE `user_studs`;
DROP TABLE `sondage`;';
			break;
		case 'pgsql':
			$query_drop_old_tables = '
DROP TABLE comments;
DROP TABLE sujet_studs;
DROP TABLE user_studs;
DROP TABLE sondage;';
			break;
	}
	$pdo->exec($query_drop_old_tables);
    }

    private function unescape($value) {
        return stripslashes(html_entity_decode($value, ENT_QUOTES));
    }
}
