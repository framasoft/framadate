<?php
use Framadate\Migration\From_0_0_to_0_8_Migration;
use Framadate\Migration\From_0_8_to_0_9_Migration;
use Framadate\Migration\Migration;
use Framadate\Utils;

include_once __DIR__ . '/../app/inc/init.php';

// List a Migration sub classes to execute
$migrations = [
    new From_0_0_to_0_8_Migration(),
    new From_0_8_to_0_9_Migration()
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

    output('Table ' . $prefixedMigrationTable . ' created.');
}

$selectStmt = $pdo->prepare('SELECT id FROM ' . $prefixedMigrationTable . ' WHERE name=?');
$insertStmt = $pdo->prepare('INSERT INTO ' . $prefixedMigrationTable . ' (name) VALUES (?)');
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
            $success[] = $className;
        } else {
            $countFailed++;
            $fail[] = $className;
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

$smarty->assign('title', _('Migration'));

$smarty->display('admin/migration.tpl');
