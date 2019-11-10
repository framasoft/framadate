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

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\Migrations\Exception\SkipMigration;
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This migration adds the column collect_users_mail in the poll table
 *
 * @package Framadate\Migration
 * @version 1.2
 */
class Version20180419170000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Add column collect_users_mail in table poll';
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     * @throws SkipMigration
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\AddColumn_collect_mail_In_poll'), 'Migration has been executed in an earlier database migration system');
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->addColumn('collect_users_mail', 'boolean', ['default' => false]);
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     */
    public function down(Schema $schema): void
    {
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->dropColumn('collect_users_mail');
    }
}
