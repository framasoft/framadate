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

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigratorConfiguration;
use Doctrine\Migrations\Tools\Console\Helper\MigrationStatusInfosHelper;
use Framadate\Utils;

require_once __DIR__ . '/../app/inc/init.php';
const MIGRATIONS_DIRECTORY = __DIR__ . '/../app/classes/Framadate/Migrations';

$executing = false;

if (isset($_POST['execute'])) {
    $executing = true;
}

$configuration = new Configuration($connect);
$configuration->setMigrationsTableName(Utils::table(MIGRATION_TABLE) . '_new');
$configuration->setMigrationsDirectory(MIGRATIONS_DIRECTORY);
$configuration->setMigrationsNamespace('DoctrineMigrations');
$configuration->registerMigrationsFromDirectory(MIGRATIONS_DIRECTORY);

$dependencyFactory = new DependencyFactory($configuration);
$migrationRepository = $dependencyFactory->getMigrationRepository();
if ($executing) {
    $migrator = $dependencyFactory->getMigrator();
    $version = $migrationRepository->getLatestVersion();
    $migrator->migrate($version, new MigratorConfiguration());
}

$status = new MigrationStatusInfosHelper($configuration, $migrationRepository);
$infos = $status->getMigrationsInfos();

$smarty->assign('countTotal', $infos['Available Migrations']);
$smarty->assign('countExecuted', $infos['Executed Migrations']);
$smarty->assign('countWaiting', $infos['New Migrations']);
$smarty->assign('executing', $executing);
$smarty->assign('title', t('Admin', 'Migration'));
$smarty->assign('time', round((microtime(true)-$_SERVER['REQUEST_TIME_FLOAT']), 4));
$smarty->display('admin/migration.tpl');
