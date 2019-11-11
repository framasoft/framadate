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
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This migration renames the column ValueMax to value_max
 *
 * @package Framadate\Migration
 * @version 1.2
 */
class Version20191111110000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Rename the column ValueMax to value_max';
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $pollTable->addColumn('value_max', 'smallint', ['default' => null, 'notnull' => false]);
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function postUp(Schema $schema): void
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $this->addSql('UPDATE ' . Utils::table('poll') . ' SET value_max = ValueMax');
        $pollTable->dropColumn('ValueMax');
    }

    /**
     * @param Schema $schema
     * @throws SchemaException
     */
    public function down(Schema $schema): void
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $pollTable->addColumn('ValueMax', 'smallint', ['default' => null, 'notnull' => false]);
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function postDown(Schema $schema): void
    {
        $pollTable = $schema->getTable(Utils::table('poll'));
        $this->addSql('UPDATE ' . Utils::table('poll') . ' SET ValueMax = value_max');
        $pollTable->dropColumn('value_max');
    }
}
