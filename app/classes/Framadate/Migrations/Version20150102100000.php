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

use Doctrine\DBAL\Schema\Schema;
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This class executes the aciton in database to migrate data from version 0.8 to 0.9.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20150102100000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'From 0.8 to 0.9 second part';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema,'Framadate\Migration\From_0_8_to_0_9_Migration'), 'Migration has been executed in an earlier database migration system');
        foreach ([Utils::table('poll'), Utils::table('comment'), Utils::table('slot'), Utils::table('vote')] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }

        $this->migrateFromSondageToPoll();
        $this->migrateFromCommentsToComment();
        $this->migrateFromSujetStudsToSlot();
        //$this->migrateFromUserStudsToVote();

        $this->dropOldTables($schema);
    }

    public function down(Schema $schema)
    {
        $sondage = $schema->createTable('sondage');
        $sondage->addColumn('id_sondage', 'string');
        $sondage->addColumn('commentaires', 'text');
        $sondage->addColumn('mail_admin', 'string', ['notnull' => false]);
        $sondage->addColumn('nom_admin', 'string', ['notnull' => false]);
        $sondage->addColumn('titre', 'text');
        $sondage->addColumn('id_sondage_admin', 'string', ['notnull' => false]);
        $sondage->addColumn('date_creation', 'datetime', ['default' => (new \DateTime())->format('Y-m-d H:i:s')]);
        $sondage->addColumn('date_fin', 'datetime', ['notnull' => false]);
        $sondage->addColumn('format', 'string', ['notnull' => false]);
        $sondage->addColumn('mailsonde', 'boolean', ['default' => false]);
        $sondage->addColumn('statut', 'integer', ['default' => '1']);
        $sondage->addUniqueIndex(['id_sondage'], 'sondage_index_id_sondage');

        $sujetStuds = $schema->createTable('sujet_studs');
        $sujetStuds->addColumn('id_sondage', 'string');
        $sujetStuds->addColumn('sujet', 'text');
        $sujetStuds->addIndex(['id_sondage'], 'sujet_studs_index_id_sondage');

        $comments = $schema->createTable('comments');
        $schema->createSequence('comments_seq');
        $comments->addColumn('id_comment', 'integer', ['autoincrement' => true]);
        $comments->addColumn('id_sondage', 'string');
        $comments->addColumn('comment', 'text');
        $comments->addColumn('usercomment', 'text', ['notnull' => false]);
        $comments->addUniqueIndex(['id_comment'], 'comments_index_id_comment');
        $comments->addIndex(['id_sondage'], 'comments_index_id_sondage');

        $userStuds = $schema->createTable('user_studs');
        $schema->createSequence('user_studs_seq');
        $userStuds->addColumn('id_users', 'integer', ['autoincrement' => true]);
        $userStuds->addColumn('nom', 'string');
        $userStuds->addColumn('id_sondage', 'string');
        $userStuds->addColumn('reponses', 'text');
        $userStuds->addUniqueIndex(['id_users'], 'user_studs_index_id_users');
        $userStuds->addIndex(['id_sondage'], 'user_studs_index_id_sondage');

        $schema->dropTable(Utils::table('poll'));
        $schema->dropTable(Utils::table('comment'));
        $schema->dropTable(Utils::table('vote'));
        $schema->dropTable(Utils::table('slot'));
    }

    private function migrateFromSondageToPoll()
    {
        $select = $this->connection->query('
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
  FROM sondage');

        $insert = $this->connection->prepare('
INSERT INTO ' . Utils::table('poll') . '
(id, admin_id, title, description, admin_name, admin_mail, creation_date, end_date, format, editable, receiveNewVotes, active)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');

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

        $prepared = $this->connection->prepare('INSERT INTO ' . Utils::table('slot') . ' (poll_id, title, moments) VALUES (?,?,?)');
        foreach ($slots as $slot) {
            $prepared->execute([
                $slot->poll_id,
                $this->unescape($slot->title),
                !empty($slot->moments) ? $this->unescape($slot->moments) : null
            ]);
        }
    }

    private function migrateFromCommentsToComment()
    {
        $select = $this->connection->query('
SELECT
    id_sondage,
    usercomment,
    comment
  FROM comments');

        $insert = $this->connection->prepare('
INSERT INTO ' . Utils::table('comment') . ' (poll_id, name, comment)
VALUES (?,?,?)');

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $insert->execute([
                $row->id_sondage,
                $this->unescape($row->usercomment),
                $this->unescape($row->comment)
            ]);
        }
    }

    private function migrateFromUserStudsToVote()
    {
        $select = $this->connection->query('
SELECT
    id_sondage,
    nom,
    REPLACE(REPLACE(REPLACE(reponses, 1, \'X\'), 2, 1), \'X\', 2) reponses
  FROM user_studs');

        $insert = $this->connection->prepare('
INSERT INTO ' . Utils::table('vote') . ' (poll_id, name, choices)
VALUES (?,?,?)');

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

    private function dropOldTables(Schema $schema)
    {
        $schema->dropTable('comments');
        $schema->dropTable('sujet_studs');
        $schema->dropTable('user_studs');
        $schema->dropTable('sondage');
    }

    private function unescape($value)
    {
        return stripslashes(html_entity_decode($value, ENT_QUOTES));
    }
}
