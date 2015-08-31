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

use Framadate\Services\InstallService;
use Framadate\Utils;

// Define values in place of config.php (that does not exists yet)
const NOMAPPLICATION = 'Framadate';
const DEFAULT_LANGUAGE = 'fr';
const IMAGE_TITRE = 'images/logo-framadate.png';
const LOG_FILE = 'admin/stdout.log';
$ALLOWED_LANGUAGES = [
    'fr' => 'Français',
    'en' => 'English',
    'es' => 'Español',
    'de' => 'Deutsch',
    'it' => 'Italiano',
];

require_once '../app/inc/init.php';
define('CONF_FILENAME', ROOT_DIR . '/app/inc/config.php');

if (file_exists(CONF_FILENAME)) {
    header(('Location: ' . Utils::get_server_name()));
    exit;
}

$error = null;

if (!empty($_POST)) {
    $installService = new InstallService();
    $result = $installService->install($_POST, $smarty);

    if ($result['status'] === 'OK') {
        header(('Location: ' . Utils::get_server_name() . '/admin/migration.php'));
        exit;
    } else {
        $error = __('Error', $result['code']);
    }
}

$smarty->assign('error', $error);
$smarty->assign('title', __('Admin', 'Installation'));
$smarty->assign('logsAreReadable', is_readable('../' . LOG_FILE));
$smarty->display('admin/install.tpl');