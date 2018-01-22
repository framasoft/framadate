<?php 

namespace Framadate\Repositories;

use Framadate\Services\LogService;

use Framadate\FramaDB;
use Framadate\Utils;


include_once __DIR__ . '/app/inc/init.php';

$logService = new LogService();
$SlotRepository = new SlotRepository($connect, $logService);

?>
