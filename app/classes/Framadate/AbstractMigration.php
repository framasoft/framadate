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
namespace Framadate;

use Doctrine\DBAL\Migrations\AbstractMigration as DoctrineAbstractMigration;
use Doctrine\DBAL\Schema\Schema;

abstract class AbstractMigration extends DoctrineAbstractMigration
{
    /**
     * @param Schema $schema
     * @param $class
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @return bool
     */
    public function legacyCheck(Schema $schema, $class)
    {
        /**
         * If there's no legacy table, we can go on
         */
        if (!$schema->hasTable(Utils::table(MIGRATION_TABLE))) {
            return false;
        }

        $migration_table = $schema->getTable(Utils::table(MIGRATION_TABLE));
        /**
         * We check the migration table
         */
        if ($migration_table->hasColumn('name')) {
            /** @var $stmt \Doctrine\DBAL\Driver\Statement */
            $stmt = $this->connection->prepare('SELECT * FROM ' . Utils::table(MIGRATION_TABLE) . ' WHERE name = ?');
            $stmt->execute([$class]);
            return $stmt->rowCount() > 0;
        }
        return false;
    }
}
