<?php
require_once '../app/inc/init.php';

$smarty->assign('title', _('Administration'));
$smarty->display('admin/index.tpl');