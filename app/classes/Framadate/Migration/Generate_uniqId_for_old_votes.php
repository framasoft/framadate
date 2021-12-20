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

use Framadate\Security\Token;
use Framadate\Utils;
use PDO;

/**
 * This migration generate uniqId for all legacy votes.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Generate_uniqId_for_old_votes implements Migration {
    public function __construct() {
    }

    public function description(): string {
        return 'Generate "uniqId" in "vote" table for all legacy votes';
    }

    public function preCondition(PDO $pdo): bool {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if tables of v0.9 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $pdo->beginTransaction();
        $this->generateUniqIdsForEmptyOnes($pdo);
        $pdo->commit();

        return true;
    }

    private function generateUniqIdsForEmptyOnes(PDO $pdo): void
    {
        $select = $pdo->query('
SELECT `id`
  FROM `' . Utils::table('vote') . '`
 WHERE `uniqid` = \'\'');

        $update = $pdo->prepare('
UPDATE `' . Utils::table('vote') . '`
   SET `uniqid` = :uniqid
 WHERE `id` = :id');

        while ($row = $select->fetch(PDO::FETCH_OBJ)) {
            $token = Token::getToken(16);
            $update->execute([
                'uniqid' => $token,
                'id' => $row->id
                             ]);
        }
    }
}
