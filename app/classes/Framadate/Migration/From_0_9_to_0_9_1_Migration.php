<?php
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * This class executes the aciton in database to migrate data from version 0.9 to 0.9.1.
 *
 * @package Framadate\Migration
 */
class From_0_9_to_0_9_1_Migration implements Migration {

    function __construct() {
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

        // Check if tables of v0.8 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
    }

    /**
     * This methode is called only one time in the migration page.
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
        ADD `receiveNewComments` TINYINT(1) DEFAULT \'0\'
        AFTER `receiveNewVotes`');
    }

}
