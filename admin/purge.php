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

include_once __DIR__ . '/../app/inc/init.php';
include_once __DIR__ . '/../bandeaux.php';

/* Variables */
/* --------- */

$message = null;

/* Services */
/*----------*/

$inputService = Services::input();
$purgeService = Services::purge();
$securityService = Services::security();

/* POST */
/*-----*/

$action = $inputService->filterName(isset($_POST['action']) ? $_POST['action'] : null);

/* PAGE */
/* ---- */

if ($action === 'purge' && $securityService->checkCsrf('admin', $_POST['csrf'])) {
    $count = $purgeService->purgeOldPolls();
    $message = __('Admin', 'Purged:') . ' ' . $count;
}

// Assign data to template
$smarty->assign('message', $message);
$smarty->assign('crsf', $securityService->getToken('admin'));

$smarty->assign('title', __('Admin', 'Purge'));

$smarty->display('admin/purge.tpl');
