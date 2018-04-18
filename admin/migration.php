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

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\MigrationStatusInfosHelper;
use Framadate\Utils;

require_once __DIR__ . '/../app/inc/init.php';

class MigrationLogger {
    private $log;

    public function __construct()
    {
        $this->log = '';
    }

    public function addLine($message)
    {
        $this->log .= $message . "\n";
    }

    public function getLog()
    {
        return $this->log;
    }
}

$executing = false;
$migration = null;
$output = '';

if (isset($_POST['execute'])) {
    $executing = true;
}

$migrationsDirectory = __DIR__ . '/../app/classes/Framadate/Migrations';
$log = new MigrationLogger();

$configuration = new Configuration($connect, new OutputWriter(function ($message) use ($log) {
    $log->addLine($message);
}));
$configuration->setMigrationsTableName(Utils::table(MIGRATION_TABLE) . '_new');
$configuration->setMigrationsDirectory($migrationsDirectory);
$configuration->setMigrationsNamespace('DoctrineMigrations');
$configuration->registerMigrationsFromDirectory($migrationsDirectory);

if ($executing) {
    $migration = new Migration($configuration);
    $migration->migrate();
    $output = trim(strip_tags($log->getLog()));
}
$infos = (new MigrationStatusInfosHelper($configuration))->getMigrationsInfos();

$smarty->assign('countTotal', $infos['Available Migrations']);
$smarty->assign('countExecuted', $infos['Executed Migrations']);
$smarty->assign('countWaiting', $infos['New Migrations']);
$smarty->assign('executing', $executing);
$smarty->assign('title', __('Admin', 'Migration'));
$smarty->assign('output', $output);
$smarty->assign('time', round((microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']), 4));
$smarty->display('admin/migration.tpl');
