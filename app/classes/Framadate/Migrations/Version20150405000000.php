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
 * This migration adds the field hidden on the poll table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20150405000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Add column "hidden" in table "vote" for version 0.9';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\AddColumn_hidden_In_poll_For_0_9'), 'Migration has been executed in an earlier database migration system');
        foreach ([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }
        $pollTable = $schema->getTable(Utils::table('poll'));

        $this->skipIf($pollTable->hasColumn('hidden'), 'Column hidden already existing in table poll');

        $pollTable->addColumn('hidden', 'boolean', ['default' => false, 'notnull' => true]);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $pollTable = $schema->getTable(Utils::table('poll'));

        $pollTable->dropColumn('hidden');
    }
}
