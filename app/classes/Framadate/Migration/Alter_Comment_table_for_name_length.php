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
 * This migration alter the comment table to set a length to the name column.
 *
 * @package Framadate\Migration
 * @version 1.0
 */
class Alter_Comment_table_for_name_length implements Migration {
    public function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description(): string {
        return 'Alter the comment table to set a length to the name column.';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    public function preCondition(PDO $pdo): bool {
        return true;
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $this->alterCommentTable($pdo);

        return true;
    }

    private function alterCommentTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('comment') . '`
        CHANGE `name` `name` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ;');
    }
}
