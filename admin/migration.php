<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
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

use Framadate\Migration\From_0_0_to_0_8_Migration;
use Framadate\Migration\From_0_8_to_0_9_Migration;
use Framadate\Migration\AddColumn_receiveNewComments_For_0_9;
use Framadate\Migration\AddColumn_uniqId_In_vote_For_0_9;
use Framadate\Migration\AddColumn_hidden_In_poll_For_0_9;
use Framadate\Migration\Generate_uniqId_for_old_votes;
use Framadate\Migration\Migration;
use Framadate\Migration\RPadVotes_from_0_8;
use Framadate\Utils;

include_once __DIR__ . '/../app/inc/init.php';

set_time_limit(300);

// List a Migration sub classes to execute
$migrations = [
    new From_0_0_to_0_8_Migration(),
    new From_0_8_to_0_9_Migration(),
    new AddColumn_receiveNewComments_For_0_9(),
    new AddColumn_uniqId_In_vote_For_0_9(),
    new AddColumn_hidden_In_poll_For_0_9(),
    new Generate_uniqId_for_old_votes(),
    new RPadVotes_from_0_8()
];
// ---------------------------------------

// Check if MIGRATION_TABLE already exists
$tables = $connect->allTables();
$pdo = $connect->getPDO();
$prefixedMigrationTable = Utils::table(MIGRATION_TABLE);

if (!in_array($prefixedMigrationTable, $tables)) {
    $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . $prefixedMigrationTable . '` (
  `id`   INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT              NOT NULL,
  `execute_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;');
}

$selectStmt = $pdo->prepare('SELECT id FROM `' . $prefixedMigrationTable . '` WHERE name=?');
$insertStmt = $pdo->prepare('INSERT INTO `' . $prefixedMigrationTable . '` (name) VALUES (?)');
$countSucceeded = 0;
$countFailed = 0;
$countSkipped = 0;

// Loop on every Migration sub classes
$success = [];
$fail = [];
foreach ($migrations as $migration) {
    $className = get_class($migration);

    // Check if $className is a Migration sub class
    if (!$migration instanceof Migration) {
        $smarty->assign('error', 'The class ' . $className . ' is not a sub class of Framadate\\Migration\\Migration.');
        $smarty->display('error.tpl');
        exit;
    }

    // Check if the Migration is already executed
    $selectStmt->execute([$className]);
    $executed = $selectStmt->rowCount();
    $selectStmt->closeCursor();

    if (!$executed && $migration->preCondition($pdo)) {
        $migration->execute($pdo);
        if ($insertStmt->execute([$className])) {
            $countSucceeded++;
            $success[] = $migration->description();
        } else {
            $countFailed++;
            $fail[] = $migration->description();
        }
    } else {
        $countSkipped++;
    }

}

$countTotal = $countSucceeded + $countFailed + $countSkipped;

$smarty->assign('success', $success);
$smarty->assign('fail', $fail);

$smarty->assign('countSucceeded', $countSucceeded);
$smarty->assign('countFailed', $countFailed);
$smarty->assign('countSkipped', $countSkipped);
$smarty->assign('countTotal', $countTotal);
$smarty->assign('time', $total_time = round((microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']), 4));

$smarty->assign('title', __('Admin', 'Migration'));

$smarty->display('admin/migration.tpl');
