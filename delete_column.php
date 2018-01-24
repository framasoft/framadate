<?php 

namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;
use Framadate\Services\LogService;

include_once __DIR__ . '/app/inc/init.php';

$logService = new LogService();

$SlotRepository = new SlotRepository($connect, $logService);


$SlotRepository->deleteByPollId($_GET['title']);

?>
