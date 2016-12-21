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
 * This migration adds the field uniqId on the vote table.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class AddColumn_uniqId_In_vote_For_0_9 implements Migration {

    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return 'Add column "uniqId" in table "vote" for version 0.9';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
	switch(DB_DRIVER_NAME) {
		case 'mysql':
			$stmt = $pdo->query('SHOW TABLES');
			break;
		case 'pgsql':
			$stmt = $pdo->query('SELECT tablename FROM pg_tables WHERE tablename !~ \'^pg_\' AND tablename !~ \'^sql_\';');
			break;
	}
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if tables of v0.9 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
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
        switch(DB_DRIVER_NAME){
            case 'mysql':
                $pdo->exec('
ALTER TABLE `' . Utils::table('vote') . '`
ADD `uniqId` CHAR(16) NOT NULL
AFTER `id`,
ADD INDEX (`uniqId`);
                ');
                break;
            case 'pgsql':
	           $pdo->exec('
CREATE TABLE IF NOT EXISTS ' . Utils::table('vote_new') . ' (
       id      BIGSERIAL       NOT NULL PRIMARY KEY,
       uniqId  CHAR(16)        NOT NULL,
       poll_id CHAR(16)        NOT NULL,
       name    VARCHAR(64)     NOT NULL,
       choices TEXT            NOT NULL
       )
');
                $pdo->exec('
INSERT INTO '. Utils::table('vote_new') . ' SELECT id, poll_id, name, choices from '. Utils::table('vote'));

                $pdo->exec('DROP TABLE ' . Utils::table('vote'));
                $pdo->exec('ALTER TABLE ' . Utils::table('vote_new') . ' RENAME TO ' . Utils::table('vote'));
                $pdo->exec('CREATE INDEX vote_uniqId_index ON ' . Utils::table('vote') . ' ( uniqId)');
                break;
       }
}
