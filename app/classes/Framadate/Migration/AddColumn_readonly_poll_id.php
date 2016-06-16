<?php

namespace Framadate\Migration;

use Framadate\Utils;
use Framadate\Security\Token;

/**
 * This migration adds the field readonly_id on the poll table
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class AddColumn_readonly_poll_id implements Migration {

    function __construct() {
    }

    public function description()
    {
        return 'Add column "readonly_id" in table "poll"';
    }

    public function preCondition(\PDO $pdo) {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if tables of v0.9 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
    }

    public function execute(\PDO $pdo) {
        $this->addReadonlyId($pdo);
        $this->fillReadonlyId($pdo);

        return true;
    }

    private function addReadonlyId(\PDO $pdo) {
        $pdo->exec('
        ALTER TABLE `'. Utils::table('poll') .'`
        ADD `readonly_id` CHAR(20) NOT NULL
        AFTER `admin_id`,
        ADD INDEX (`readonly_id`);');
    }

    public function fillReadonlyId(\PDO $pdo) {
        $select = $pdo->query('
        SELECT `id`
        FROM `'. Utils::table('poll') .'`
        WHERE `readonly_id` = \'\'');

        $update = $pdo->prepare('
        UPDATE `'. Utils::table('poll') .'`
        SET `readonly_id` = :readonly_id
        WHERE `id` = :id');

        $readonly_ids = [];
        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            do {
                $readonly_id = Token::getToken(20);;
            } while (in_array($readonly_id, $readonly_ids));
            $readonly_ids[] = $readonly_id;

            $update->execute([
                'readonly_id' => $readonly_id,
                'id' => $row->id
            ]);
        }
    }
}
