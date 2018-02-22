<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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

require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/../../vendor/autoload.php';

$debug = true;

$loader = new Twig_Loader_Filesystem( __DIR__ . '/../../tpl');
$twig = new Twig_Environment($loader, [
    'cache' => $debug === false ?: __DIR__ . '/../../tpl_c',
    'debug' => $debug,
]);

$trans = new Twig_SimpleFunction('__', function ($section, $key, $args = []) use ($i18n) {
    if ($args === []) {
        return $i18n->get($section, $key);
    }
    return $i18n->format($section, $key, $args);
});

$route = new Twig_SimpleFunction('poll_url', function ($id, $admin = false, $action = false, $action_value = false, $vote_id = '') {
    $poll_id =  filter_var($id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $action = $action === false ?: Utils::htmlEscape($action);
    $action_value = $action_value === false ?: $action_value;
    $vote_unique_id = $vote_id === '' ?: filter_var($vote_id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);

    // If filter_var fails (i.e.: hack tentative), it will return false. At least no leak is possible from this.

    return Utils::getUrlSondage($poll_id, $admin, $vote_unique_id, $action, $action_value);
});

$preg_match = new Twig_SimpleFunction('preg_match', function($pattern, $subject) {
   return preg_match($pattern, $subject);
});

$twig->addFunction($trans);
$twig->addFunction($route);
$twig->addFunction($preg_match);

$twig->addExtension(new Twig_Extension_Debug());

$twig->addGlobal('APPLICATION_NAME', NOMAPPLICATION);
$twig->addGlobal('SERVER_URL', Utils::get_server_name());
$twig->addGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
$twig->addGlobal('TITLE_IMAGE', IMAGE_TITRE);
$twig->addGlobal('use_nav_js', strstr($_SERVER['SERVER_NAME'], 'framadate.org'));
$twig->addGlobal('locale', $locale);
$twig->addGlobal('langs', $ALLOWED_LANGUAGES);
$twig->addGlobal('date_format', $date_format);
if (isset($config['tracking_code'])) {
    $twig->addGlobal('tracking_code', $config['tracking_code']);
}
if (defined('FAVICON')) {
    $twig->addGlobal('favicon', FAVICON);
}


function smarty_modifier_markdown($md, $clear = false, $inline=true) {
    return Utils::markdown($md, $clear, $inline);
}

function smarty_modifier_resource($link) {
    return Utils::get_server_name() . $link;
}
function smarty_modifier_addslashes_single_quote($string) {
    return addcslashes($string, '\\\'');
}

function smarty_modifier_html($html) {
    return Utils::htmlEscape($html);
}
