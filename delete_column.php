<?php 

namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;
use Framadate\Services\LogService;

include_once __DIR__ . '/app/inc/init.php';

$logService = new LogService();

$SlotRepository = new SlotRepository($connect, $logService);

$SlotRepository->deleteByPollId($_GET['title']);

$column = $_GET['title'];

$poll = $_GET['poll_id'];

$accessGranted = $_GET['acce'];

$smarty->assign('accessGranted',$accessGranted);

$smarty->assign('poll',$poll);

$smarty->assign('column',$column);

$smarty->display("./tpl/delete_column.tpl");

?>
