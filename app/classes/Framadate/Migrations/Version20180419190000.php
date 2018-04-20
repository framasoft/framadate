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
 * This migration adds the column mail in the vote table
 *
 * @package Framadate\Migration
 * @version 1.2
 */
class Version20180419190000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Remove the old migration table';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $schema->dropTable(Utils::table(MIGRATION_TABLE));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $migrationTable = $schema->createTable(Utils::table(MIGRATION_TABLE));
        $schema->createSequence(Utils::table(MIGRATION_TABLE) . 'seq');
        $migrationTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $migrationTable->addColumn('name', 'string');
        $migrationTable->addColumn('execute_date', 'datetime', ['default' => (new \DateTime())->format('Y-m-d H:i:s')]);
    }
}
