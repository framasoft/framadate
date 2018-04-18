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

/**
 * Class From_0_0_to_0_8_Migration
 *
 * @package Framadate\Migration
 * @version 0.8
 */
class Version20150101000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'First installation of the Framadate application (v0.8)';
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @return void true is the execution succeeded
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema,'Framadate\Migration\From_0_0_to_0_8_Migration'), 'Migration has been executed in an earlier database migration system');
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
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE sondage');
        $this->addSql('DROP TABLE sujet_studs');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE user_studs');
    }
}
