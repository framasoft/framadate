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
 * This migration adds the field Value_Max on the poll table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class AddColumn_closed_In_poll implements Migration {
    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return 'Add column "Closed" in table "poll" ';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
        return true;
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $this->alterPollTable($pdo);

        return true;
    }

    private function alterPollTable(\PDO $pdo) {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('poll') . '`
        ADD `closed` TINYINT DEFAULT 0;');
    }
}
