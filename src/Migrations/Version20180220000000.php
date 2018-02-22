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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Framadate\Utils;

/**
 * This migration adds the field Value_Max on the poll table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20180220000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Add column "ValueMax" in table "vote" for version 0.9';
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param Connection $connect The connection to database
     * @return bool true is the execution succeeded
     */
    public function execute(Connection $connect)
    {
        $this->alterPollTable($connect);

        return true;
    }

    private function alterPollTable(Connection $connect)
    {
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $pollTable->addColumn('ValueMax', 'smallint');
        $this->addSql('ALTER TABLE ' . Utils::table('poll') . ' ADD CHECK (ValueMax > 0)');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $pollTable->dropColumn('ValueMax');
    }
}
