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
use Doctrine\DBAL\Schema\Schema;
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This migration sets Poll.end_date to NULL by default
 *
 * @package Framadate\Migration
 * @version 1.1
 */
class Version20180411000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Sets Poll end_date to NULL by default (work around MySQL NO_ZERO_DATE)';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param Connection|\PDO $connection The connection to database
     * @return bool true if the Migration should be executed.
     */
    public function preCondition(Connection $connection)
    {
        $driver_name = $connection->getWrappedConnection()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($driver_name === 'mysql') {
            $stmt = $connection->prepare(
                "SELECT Column_Default from Information_Schema.Columns where Table_Name = ? AND Column_Name = ?;"
            );
            $stmt->bindValue(1, Utils::table('poll'));
            $stmt->bindValue(2, 'end_date');
            $stmt->execute();
            $default = $stmt->fetch(\PDO::FETCH_COLUMN);

            return $default === null;
        }
        return true;
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        // We don't disable this migration even if legacy because it wasn't working correctly before
        // $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\Fix_MySQL_No_Zero_Date'), 'Migration has been executed in an earlier database migration system');
        $this->skipIf($this->preCondition($this->connection), "Database server isn't MySQL or poll end_date default value was already NULL");
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->changeColumn('end_date', ['default' => null, 'notnull' => false]);
    }

    public function down(Schema $schema)
    {
        // nothing
    }
}
