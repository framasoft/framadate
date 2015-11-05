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
$smarty->assign('use_nav_js', strstr($_SERVER['SERVER_NAME'], 'framadate.org'));
$smarty->assign('locale', $locale);
$smarty->assign('langs', $ALLOWED_LANGUAGES);
$smarty->assign('date_format', $date_format);

// Dev Mode
if (isset($_SERVER['FRAMADATE_DEVMODE']) && $_SERVER['FRAMADATE_DEVMODE']) {
    $smarty->force_compile = true;
    $smarty->compile_check = true;

} else {
    $smarty->force_compile = false;
    $smarty->compile_check = false;
}


function smarty_function_poll_url($params, Smarty_Internal_Template $template) {
    $poll_id =  filter_var($params['id'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $admin =  (isset($params['admin']) && $params['admin']) ? true : false;
    $action =  (isset($params['action']) && !empty($params['action'])) ? Utils::htmlEscape($params['action']) : false;
    $action_value = (isset($params['action_value']) && !empty($params['action_value'])) ? $params['action_value'] : false;
    $vote_unique_id = isset($params['vote_id']) ? filter_var($params['vote_id'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]) : '';

    // If filter_var fails (i.e.: hack tentative), it will return false. At least no leak is possible from this.

    return Utils::getUrlSondage($poll_id, $admin, $vote_unique_id, $action, $action_value);
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