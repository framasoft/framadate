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

use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\PurgeService;
use Framadate\Services\SecurityService;

include_once __DIR__ . '/../app/inc/init.php';
include_once __DIR__ . '/../bandeaux.php';

/* Variables */
/* --------- */

$message = null;

/* Services */
/*----------*/

$logService = new LogService();
$purgeService = new PurgeService($connect, $logService);
$securityService = new SecurityService();
$inputService = new InputService();

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

echo $twig->render('admin/purge.twig', [
    'message' => $message,
    'crsf' => $securityService->getToken('admin'),
    'title' => __('Admin', 'Purge'),
]);
