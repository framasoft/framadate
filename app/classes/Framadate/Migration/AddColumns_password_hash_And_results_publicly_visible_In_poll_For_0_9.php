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
 * This migration adds the fields password_hash and results_publicly_visible on the poll table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class AddColumns_password_hash_And_results_publicly_visible_In_poll_For_0_9 implements Migration {
    public function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description(): string {
        return 'Add columns "password_hash" and "results_publicly_visible" in table "vote" for version 0.9';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    public function preCondition(PDO $pdo): bool {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if tables of v0.9 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $this->alterPollTable($pdo);

        return true;
    }

    private function alterPollTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('poll') . '`
        ADD `password_hash` VARCHAR(255) NULL DEFAULT NULL ,
        ADD `results_publicly_visible` TINYINT(1) NULL DEFAULT NULL');
    }
}
