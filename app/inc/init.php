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
use Framadate\FramaDB;

// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_id() == '') {
    session_start();
}

if (ini_get('date.timezone') == '') {
    date_default_timezone_set('Europe/Paris');
}

define('ROOT_DIR', __DIR__ . '/../../');

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/i18n.php';

// Smarty
require_once __DIR__ . '/smarty.php';

// Connection to database
$connect = new FramaDB(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
$err = 0;
