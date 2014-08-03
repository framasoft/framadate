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

if (isset($_POST['lang']) && is_string($_POST['lang']) && in_array($_POST['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
    $mlocale = $_POST['lang'] ;
    setcookie('lang' , $_POST['lang'], time()+60*5);
} elseif ( isset($_COOKIE['lang']) && is_string($_COOKIE['lang']) && in_array($_COOKIE['lang'], array_keys($ALLOWED_LANGUAGES)) ) {
    $mlocale = $_COOKIE['lang'] ;
} else {
    $mlocale = LANGUE ;
}

$locale = $mlocale . '.utf8';
putenv('LANGUAGE=');
setlocale(LC_ALL, $locale);
setlocale(LC_TIME, $locale);
setlocale(LC_MESSAGES, $locale);

$domain = 'Studs';
bindtextdomain($domain, 'locale');
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

/* temp, for compatibility :*/
$a = explode('_', $locale);
$_SESSION['langue'] = strtoupper($a[0]);

/* <html lang="$lang"> */
$lang = ($_SESSION['langue']!='') ? strtolower($_SESSION['langue']) : 'fr';

