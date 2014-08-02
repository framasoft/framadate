<?php

if (ini_get('date.timezone') == '') {
    date_default_timezone_set('Europe/Paris');
}
// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';

include_once __DIR__ . '/constants.php';
include_once __DIR__ . '/i18n.php';


$connect = NewADOConnection(BASE_TYPE);
$connect->Connect(SERVEURBASE, USERBASE, USERPASSWD, BASE);
$err = 0;
