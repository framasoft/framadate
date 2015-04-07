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

use Framadate\Services\AdminPollService;
use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Services\SuperAdminService;

include_once __DIR__ . '/../app/inc/init.php';
include_once __DIR__ . '/../bandeaux.php';

const POLLS_PER_PAGE = 30;

/* Functions */

function buildSearchQuery($search) {
    $query = '';
    foreach ($search as $key => $value) {
        $query .= $key . '=' . urlencode($value) . '&';
    }
    return substr($query, 0, -1);
}

/* --------- */

/* Variables */
/* --------- */

$polls = null;
$poll_to_delete = null;

/* Services */
/*----------*/

$logService = new LogService();
$pollService = new PollService($connect, $logService);
$adminPollService = new AdminPollService($connect, $pollService, $logService);
$superAdminService = new SuperAdminService();
$securityService = new SecurityService();

/* GET */
/*-----*/
$page = (int)filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
$page = ($page >= 1) ? $page : 1;

// Search
$search['poll'] = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
$search['title'] = filter_input(INPUT_GET, 'title', FILTER_SANITIZE_STRING);
$search['name'] = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

/* PAGE */
/* ---- */

if (!empty($_POST['delete_poll']) && $securityService->checkCsrf('admin', $_POST['csrf'])) {
    $delete_id = filter_input(INPUT_POST, 'delete_poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll_to_delete = $pollService->findById($delete_id);
}

// Traitement de la confirmation de suppression
if (!empty($_POST['delete_confirm']) && $securityService->checkCsrf('admin', $_POST['csrf'])) {
    $poll_id = filter_input(INPUT_POST, 'delete_confirm', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $adminPollService->deleteEntirePoll($poll_id);
}

$found = $superAdminService->findAllPolls($search, $page - 1, POLLS_PER_PAGE);
$polls = $found['polls'];
$count = $found['count'];
$total = $found['total'];

// Assign data to template
$smarty->assign('polls', $polls);
$smarty->assign('count', $count);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('pages', ceil($count / POLLS_PER_PAGE));
$smarty->assign('poll_to_delete', $poll_to_delete);
$smarty->assign('crsf', $securityService->getToken('admin'));
$smarty->assign('search', $search);
$smarty->assign('search_query', buildSearchQuery($search));

$smarty->assign('title', __('Admin', 'Polls'));

$smarty->display('admin/polls.tpl');
