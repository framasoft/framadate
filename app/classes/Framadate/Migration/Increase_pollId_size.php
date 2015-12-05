<?php
namespace Framadate\Migration;

use Framadate\Utils;

class Increase_pollId_size implements Migration {

    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return 'Increase the size of id column in poll table';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true if the Migration should be executed
     */
    function preCondition(\PDO $pdo) {
        return true;
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true if the execution succeeded
     */
    function execute(\PDO $pdo) {
        $this->alterPollTable($pdo);
    }

    private function alterPollTable(\PDO $pdo) {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('poll') . '`
        CHANGE `id` `id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }
}
