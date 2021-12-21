<?php
namespace Framadate\Migration;

use Framadate\Utils;
use PDO;

class Increase_pollId_size implements Migration {
    public function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description(): string {
        return 'Increase the size of id column in poll table';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param PDO $pdo The connection to database
     * @return bool true if the Migration should be executed
     */
    public function preCondition(PDO $pdo): bool {
        return true;
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool true if the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $this->alterCommentTable($pdo);
        $this->alterPollTable($pdo);
        $this->alterSlotTable($pdo);
        $this->alterVoteTable($pdo);
        return true;
    }

    private function alterCommentTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('comment') . '`
        CHANGE `poll_id` `poll_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }

    private function alterPollTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('poll') . '`
        CHANGE `id` `id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }

    private function alterSlotTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('slot') . '`
        CHANGE `poll_id` `poll_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }

    private function alterVoteTable(PDO $pdo): void
    {
        $pdo->exec('
        ALTER TABLE `' . Utils::table('vote') . '`
        CHANGE `poll_id` `poll_id` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }
}
