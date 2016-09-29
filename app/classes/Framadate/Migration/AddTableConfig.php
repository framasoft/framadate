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
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * Issue121 : This migration adds the 'Config' table for global configuration in framadate.
 *
 * @package Framadate\Migration
 * @version 0.9.x
 */
class AddTableConfig implements Migration {

    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return "Add 'Config' table for global configuration in framadate";
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if destination tables are presents
        $diff = array_diff([Utils::table('Config')], $tables);
        return count($diff) != 0;		//!= 0 : At least one table is not present => OK for creation
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $pdo->exec('CREATE TABLE IF NOT EXISTS `' . Utils::table('Config') . '` ('
                  .' name  VARCHAR(64) NOT NULL,'
                  .' value TEXT        DEFAULT NULL,'
                  .' PRIMARY KEY (name)'
                  .') ENGINE = InnoDB DEFAULT CHARSET = utf8'
                  );

        $pdo->exec('INSERT INTO `' . Utils::table('Config') . '` (name, value) values (\'admin_pwd\', null)');

        return true;
    }
}
