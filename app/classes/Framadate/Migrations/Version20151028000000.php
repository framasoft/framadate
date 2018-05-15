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
 * This migration adds the fields password_hash and results_publicly_visible on the poll table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20151028000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Add columns "password_hash" and "results_publicly_visible" in table "vote" for version 0.9';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\AddColumns_password_hash_And_results_publicly_visible_In_poll_For_0_9'), 'Migration has been executed in an earlier database migration system');
        $pollTable = $schema->getTable(Utils::table('poll'));

        $this->skipIf($pollTable->hasColumn('password_hash'), 'Column password_hash in table poll already exists');
        $this->skipIf($pollTable->hasColumn('results_publicly_visible'), 'Column results_publicly_visible in table poll already exists');

        $pollTable->addColumn('password_hash', 'string', ['notnull' => false]);
        $pollTable->addColumn('results_publicly_visible', 'boolean', ['notnull' => false]);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $pollTable = $schema->getTable(Utils::table('poll'));

        $pollTable->dropColumn('password_hash');
        $pollTable->dropColumn('results_publicly_visible');
    }
}
