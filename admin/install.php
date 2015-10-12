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

require_once '../app/inc/init.php';

if (is_file(CONF_FILENAME)) {
    header(('Location: ' . Utils::get_server_name()));
    exit;
}

$error = null;
$installService = new InstallService();

if (!empty($_POST)) {
    $installService->updateFields($_POST);
    $result = $installService->install($smarty);

    if ($result['status'] === 'OK') {
        header(('Location: ' . Utils::get_server_name() . 'admin/migration.php'));
        exit;
    } else {
        $error = __('Error', $result['code']);
    }
}

$smarty->assign('error', $error);
$smarty->assign('title', __('Admin', 'Installation'));
$smarty->assign('fields', $installService->getFields());
$smarty->display('admin/install.tpl');