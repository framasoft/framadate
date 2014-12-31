<?php
use Framadate\Migration\From_0_8_to_0_9_Migration;
use Framadate\Migration\Migration;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

function output($msg) {
    echo $msg . '<br/>';
}

// List a Migration sub classes to execute
$migrations = [
    new From_0_8_to_0_9_Migration(),
    new From_0_8_to_0_9_Migration()
];

// Check if MIGRATION_TABLE already exists
$tables = $connect->allTables();
$pdo = $connect->getPDO();

if (!in_array(MIGRATION_TABLE, $tables)) {
    $pdo->exec('
CREATE TABLE IF NOT EXISTS `' . MIGRATION_TABLE . '` (
  `id`   INT(11)  UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TEXT              NOT NULL,
  `execute_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;');

    output('Table ' . MIGRATION_TABLE . ' created.');
}

$selectStmt = $pdo->prepare('SELECT id FROM ' . MIGRATION_TABLE . ' WHERE name=?');
$insertStmt = $pdo->prepare('INSERT INTO ' . MIGRATION_TABLE . ' (name) VALUES (?)');

// Loop on every Migration sub classes
foreach ($migrations as $migration) {
    $className = get_class($migration);

    // Check if $className is a Migration sub class
    if (!$migration instanceof Migration) {
        output('The class '. $className . ' is not a sub class of Framadate\\Migration\\Migration.');
        exit;
    }

    // Check if the Migration is already executed
    $selectStmt->execute([$className]);
    $executed = $selectStmt->rowCount();
    $selectStmt->closeCursor();

    if (!$executed) {
        $migration->execute($pdo);
        $insertStmt->execute([$className]);
        output('Migration done: ' . $className);
    }

}
