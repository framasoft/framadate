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
 * This migration alter the comment table to add a date column.
 *
 * @package Framadate\Migration
 * @version 1.0
 */
class Version20151012082600 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Alter the comment table to add a date column.';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\Alter_Comment_table_adding_date'), 'Migration has been executed in an earlier database migration system');
        $commentTable = $schema->getTable(Utils::table('comment'));

        $this->skipIf($commentTable->hasColumn('date'), 'Column date in comment table already exists');

        // The default for datetime types is only available for MySQL 5.6
        // See the "Functionality Added or Changed" section,
        // "Important Change: In MySQL, the TIMESTAMP data type" element, on
        // https://dev.mysql.com/doc/relnotes/mysql/5.6/en/news-5-6-6.html
        $commentTable->addColumn('date', 'datetime', ['default' => 0]);
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $commentTable = $schema->getTable(Utils::table('comment'));

        $commentTable->dropColumn('comment');
    }
}
