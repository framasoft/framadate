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
use Framadate\Utils;

require_once __DIR__ . '/../../vendor/smarty/smarty/libs/Smarty.class.php';
$smarty = new \Smarty();
$smarty->setTemplateDir(ROOT_DIR . '/tpl/');
$smarty->setCompileDir(ROOT_DIR . '/tpl_c/');
$smarty->setCacheDir(ROOT_DIR . '/cache/');
$smarty->caching = false;

$smarty->assign('APPLICATION_NAME', NOMAPPLICATION);
$smarty->assign('SERVER_URL', Utils::get_server_name());
$smarty->assign('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
$smarty->assign('TITLE_IMAGE', IMAGE_TITRE);
$smarty->assign('use_nav_js', file_exists($_SERVER['DOCUMENT_ROOT'] . '/nav/nav.js'));
$smarty->assign('html_lang', $html_lang);
$smarty->assign('langs', $ALLOWED_LANGUAGES);
$smarty->assign('date_format', $date_format);

function smarty_modifier_poll_url($poll_id, $admin = false) {
    return Utils::getUrlSondage($poll_id, $admin);
}

function smarty_modifier_markdown($md, $clear = false) {
    return Utils::markdown($md, $clear);
}

function smarty_modifier_resource($link) {
    return Utils::get_server_name() . $link;
}

function smarty_modifier_html($html) {
    return Utils::htmlEscape($html);
}
