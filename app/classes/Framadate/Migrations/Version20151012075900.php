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
use Doctrine\DBAL\Types\Type;
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This migration alter the comment table to set a length to the name column.
 *
 * @package Framadate\Migration
 * @version 1.0
 */
class Version20151012075900 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Alter the comment table to set a length to the name column.';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\Alter_Comment_table_for_name_length'), 'Migration has been executed in an earlier database migration system');
        $commentTable = $schema->getTable(Utils::table('comment'));

        $commentTable->changeColumn('name', ['default' => null, 'notnull' => false]);

        $commentTable->changeColumn('name', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true]);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down(Schema $schema)
    {
        $commentTable = $schema->getTable(Utils::table('comment'));

        $commentTable->changeColumn('name', ['type' => Type::getType('string')]);
    }
}
