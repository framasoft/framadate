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

use Framadate\Services\AdminPollService;
use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Services\SuperAdminService;

include_once __DIR__ . '/app/inc/init.php';
include_once __DIR__ . '/bandeaux.php';

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
$poll_to_delete1 = null;
$delete = false;
$passerror = false;
$passvide = false;
$passvalide = false;
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
$search['mail'] = filter_input(INPUT_GET, 'mail', FILTER_SANITIZE_STRING);

/* PAGE */
/* ---- */

if (!empty($_POST['delete_poll']) && $securityService->checkCsrf('admin', $_POST['csrf'])) {
 $delete_id = filter_input(INPUT_POST, 'delete_poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
 $poll_to_delete = $pollService->findById($delete_id);
}

$p = 0;

// Traitement de la confirmation de suppression
if (!empty($_POST['delete_confirm']) && $securityService->checkCsrf('admin', $_POST['csrf'])) {

 $poll_id = filter_input(INPUT_POST, 'delete_confirm', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);

if(isset($_POST['passadmin']) && !empty($_POST['passadmin'])){

 $passwordadmin = $pollService->findById($_GET['d']);
 $passwordadmin = $passwordadmin->password_admin;
 
 
 if (password_verify($_POST['passadmin'], $passwordadmin)) {
  
  $adminPollService->deleteEntirePoll($_GET['d']);
    
$delete = true;
  $passvalide = true;
}else{

$passerror = true;
$passvalide = false;
}

}else{

$passvide = true;

}

}


$found = $superAdminService->findAllPolls($search, $page - 1, POLLS_PER_PAGE);
$polls = $found['polls'];
$count = $found['count'];
$total = $found['total'];

$d =  htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

if(isset($_POST['delete_poll']) && !empty($_POST['delete_poll'])){
if(!isset($_GET['d'])){
$d =  htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?d=".$_POST['delete_poll']);
}
else{

$d =  htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}
}else{
$d =  htmlspecialchars("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
}

if(isset($_POST['delete_poll']) && !empty($_POST['delete_poll']) && isset($_GET['poll_id']) && !empty($_GET['poll_id']) && $_GET['poll_id'] != $_POST['delete_poll']){

$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?poll_id=".$_POST['delete_poll'];

$pieces = explode("?", $url);

$b =  $pieces[0]."?poll_id=".$_POST['delete_poll'];

$d =  htmlspecialchars($b);

}

if(!empty($_POST['delete_confirm']) ){

$poll_to_delete1 ='true';

}


// Assign data to template
$smarty->assign('polls', $polls);
$smarty->assign('count', $count);
$smarty->assign('total', $total);
$smarty->assign('page', $page);
$smarty->assign('pages', ceil($count / POLLS_PER_PAGE));
$smarty->assign('poll_to_delete', $poll_to_delete);
$smarty->assign('poll_to_delete1', $poll_to_delete1);
$smarty->assign('crsf', $securityService->getToken('admin'));
$smarty->assign('search', $search);
$smarty->assign('search_query', buildSearchQuery($search));
$smarty->assign('f',$d);
$smarty->assign('delete',$delete);
$smarty->assign('passerror',$passerror);
$smarty->assign('passvide',$passvide);
$smarty->assign('passvalide',$passvalide);
if(isset($_GET['d'])){

$smarty->assign('idc',$_GET['d']);

}

if(isset($_POST['delete_poll']) && !empty($_POST['delete_poll'])){
$smarty->assign('idc',$_POST['delete_poll']);
}


$smarty->assign('title', __('Admin', 'Polls'));

$smarty->display('admin/polls.tpl');
