<?php 

namespace Framadate\Repositories;

use Framadate\Services\LogService;

use Framadate\FramaDB;
use Framadate\Utils;


include_once __DIR__ . '/app/inc/init.php';

$logService = new LogService();
$SlotRepository = new SlotRepository($connect, $logService);

$format  = $_GET['format'];

$poll = $_GET['poll'];

$accessGranted = $_GET['acces'];

$smarty->assign('poll',$poll);

$smarty->assign('format', $format);

$smarty->assign('accessGranted',$accessGranted);

$smarty->display('add_column.tpl');

?>
