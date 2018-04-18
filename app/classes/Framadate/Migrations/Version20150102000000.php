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
class Version20150102000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'From 0.8 to 0.9 first part';
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
        foreach (['sondage', 'sujet_studs', 'comments', 'user_studs'] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }

        $this->createPollTable($schema);
        $this->createCommentTable($schema);
        $this->createSlotTable($schema);
        $this->createVoteTable($schema);
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

    private function createPollTable(Schema $schema)
    {
        $poll = $schema->createTable(Utils::table('poll'));
        $poll->addColumn('id', 'string');
        $poll->addColumn('admin_id', 'string');
        $poll->addColumn('title', 'text');
        $poll->addColumn('description', 'text', ['notnull' => false]);
        $poll->addColumn('admin_name', 'string');
        $poll->addColumn('admin_mail', 'string', ['notnull' => false]);
        $poll->addColumn('creation_date', 'datetime', ['default' => (new \DateTime())->format('Y-m-d H:i:s')]);
        $poll->addColumn('end_date', 'datetime', ['notnull' => false]);
        $poll->addColumn('format', 'string', ['default' => null, 'notnull' => false]);
        $poll->addColumn('editable', 'integer', ['default' => 0]);
        $poll->addColumn('receiveNewVotes', 'boolean', ['default' => false]);
        $poll->addColumn('active', 'boolean', ['default' => true]);
        $poll->addUniqueIndex(['id'], 'poll_index_id');
    }

    private function createSlotTable(Schema $schema)
    {
        $slot = $schema->createTable(Utils::table('slot'));
        $schema->createSequence('slot_seq');
        $slot->addColumn('id', 'integer', ['autoincrement' => true]);
        $slot->addColumn('poll_id', 'string');
        $slot->addColumn('title', 'text');
        $slot->addColumn('moments', 'text', ['notnull' => false]);
        $slot->addUniqueIndex(['id'], 'slot_index_id');
        $slot->addIndex(['poll_id'], 'slot_index_poll_id');
    }

    private function createCommentTable(Schema $schema)
    {
        $comment = $schema->createTable(Utils::table('comment'));
        $schema->createSequence('comment_seq');
        $comment->addColumn('id', 'integer', ['autoincrement' => true]);
        $comment->addColumn('poll_id', 'string');
        $comment->addColumn('name', 'text', ['notnull' => false]);
        $comment->addColumn('comment', 'text');
        $comment->addUniqueIndex(['id'], 'comment_index_id');
        $comment->addIndex(['poll_id'], 'comment_index_poll_id');
    }

    private function createVoteTable(Schema $schema)
    {
        $vote = $schema->createTable(Utils::table('vote'));
        $schema->createSequence('vote_seq');
        $vote->addColumn('id', 'integer', ['autoincrement' => true]);
        $vote->addColumn('poll_id', 'string');
        $vote->addColumn('name', 'string');
        $vote->addColumn('choices', 'string');
        $vote->addUniqueIndex(['id'], 'vote_index_id');
        $vote->addIndex(['poll_id'], 'vote_index_poll_id');
    }
}
