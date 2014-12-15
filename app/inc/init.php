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
use Framadate\Utils;

if (ini_get('date.timezone') == '') {
    date_default_timezone_set('Europe/Paris');
}
include_once __DIR__ . '/constants.php';
include_once __DIR__ . '/i18n.php';
include_once __DIR__ . '/studs.inc.php';

// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Smarty
require_once __DIR__ . '/../../vendor/smarty/smarty/libs/Smarty.class.php';
$smarty = new \Smarty();
$smarty->template_dir = 'tpl/';
$smarty->compile_dir = 'tpl_c/';
$smarty->cache_dir = 'cache/';
$smarty->caching = false;

$smarty->assign('APPLICATION_NAME', NOMAPPLICATION);
$smarty->assign('SERVER_URL', Utils::get_server_name());
$smarty->assign('TITLE_IMAGE', IMAGE_TITRE);
$smarty->assign('use_nav_js', file_exists($_SERVER['DOCUMENT_ROOT'] . '/nav/nav.js'));
$smarty->assign('lang', $lang);
$smarty->assign('langs', $ALLOWED_LANGUAGES);
$smarty->assign('date_format', $date_format);

function smarty_modifier_poll_url($poll_id, $admin=false){return Utils::getUrlSondage($poll_id, $admin);}
// End- Smarty

if (session_id() == '') {
    session_start();
}

$connect = new FramaDB(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
$err = 0;
