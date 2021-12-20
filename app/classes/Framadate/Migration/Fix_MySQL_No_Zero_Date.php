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
use PDO;

/**
 * This migration sets Poll.end_date to NULL by default
 *
 * @package Framadate\Migration
 * @version 1.1
 */
class Fix_MySQL_No_Zero_Date implements Migration {
    public function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description(): string {
        return 'Sets Poll end_date to NULL by default (work around MySQL NO_ZERO_DATE)';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param PDO $pdo The connection to database
     * @return bool true if the Migration should be executed.
     */
    public function preCondition(PDO $pdo): bool {
        $stmt = $pdo->prepare("SELECT Column_Default from Information_Schema.Columns where Table_Name = ? AND Column_Name = ?;");
        $stmt->bindValue(1, Utils::table('poll'));
        $stmt->bindValue(2, 'end_date');
        $stmt->execute();
        $default = $stmt->fetch(PDO::FETCH_COLUMN);

        $driver_name = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        return $default !== null && $driver_name === 'mysql';
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool if the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $pdo->exec('ALTER TABLE ' . Utils::table('poll') . ' MODIFY end_date TIMESTAMP NULL DEFAULT NULL;');
        return true;
    }
}
