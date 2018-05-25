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
 * This migration adds the column collect_users_mail in the poll table
 *
 * @package Framadate\Migration
 * @version 1.2
 */
class Version20180525110000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Change column collect_users_mail in table poll from boolean to smallint';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->addColumn('collect_users_mail_integer', 'smallint', ['default' => 0]);
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->addSql('UPDATE ' . Utils::table('poll') . ' SET collect_users_mail_integer = collect_users_mail');
        $this->addSql('ALTER TABLE ' . Utils::table('poll') . ' DROP COLUMN collect_users_mail');
        if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
            $this->addSql(
                'ALTER TABLE ' . Utils::table('poll') . ' CHANGE collect_users_mail_integer collect_users_mail SMALLINT'
            );
        } else {
            $this->addSql(
                'ALTER TABLE ' . Utils::table('poll') . ' RENAME COLUMN collect_users_mail_integer to collect_users_mail'
            );
        }
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->addColumn('collect_users_mail_boolean', 'boolean', ['default' => false]);
    }

    /**
     * @param Schema $schema
     */
    public function postDown(Schema $schema)
    {
        $this->addSql('UPDATE ' . Utils::table('poll') . ' SET collect_users_mail_boolean = collect_users_mail > 0');
        $this->addSql('ALTER TABLE ' . Utils::table('poll') . ' DROP COLUMN collect_users_mail');

        if ($this->connection->getDatabasePlatform()->getName() === 'mysql') {
            $this->addSql(
                'ALTER TABLE ' . Utils::table('poll') . ' CHANGE collect_users_mail_boolean collect_users_mail SMALLINT'
            );
        } else {
            $this->addSql(
                'ALTER TABLE ' . Utils::table('poll') . ' RENAME COLUMN collect_users_mail_boolean to collect_users_mail'
            );
        }
    }
}
