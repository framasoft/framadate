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

asort($ALLOWED_LANGUAGES);

if (isset($_POST['lang']) && is_string($_POST['lang']) && in_array($_POST['lang'], array_keys($ALLOWED_LANGUAGES))) {
    $mlocale = $_POST['lang'];
    $_SESSION['lang'] = $_POST['lang'];
} elseif (isset($_SESSION['lang']) && is_string($_SESSION['lang']) && in_array($_SESSION['lang'], array_keys($ALLOWED_LANGUAGES))) {
    $mlocale = $_SESSION['lang'];
} else {

    $mlocale = LANGUE;
    // Replace config language by browser language if possible
    foreach ($ALLOWED_LANGUAGES as $k => $v) {
        if (substr($k, 0, 2) == substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)) {
            $mlocale = $k;
            break;
        }
    }

}

/* Tell PHP which locale to use */
$domain = 'Studs';
$locale = $mlocale . '.utf8'; //unix format

if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
    putenv("LC_ALL=$mlocale"); //Windows env. needed to switch between languages
    switch ($mlocale) {
        case 'fr_FR' :
            $locale = "fra";
            break; //$locale in windows locale format, needed to use php function that handle text : strftime()
        case 'en_GB' :
            $locale = "english";
            break; //see http://msdn.microsoft.com/en-us/library/39cwe7zf%28v=vs.90%29.aspx
        case 'de_DE' :
            $locale = "deu";
            break;
        case 'es_ES' :
            $locale = "esp";
            break;
    }
}
putenv('LANG=' . $locale);
setlocale(LC_ALL, $locale);
bindtextdomain($domain, ROOT_DIR . 'locale');
bind_textdomain_codeset($domain, 'UTF-8');
textdomain($domain);

/* <html lang="$html_lang"> */
$html_lang = substr($locale, 0, 2);

/* Date Format */
$date_format['txt_full'] = _('%A, den %e. %B %Y'); //summary in choix_date.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = _('%A %e %B %Y'); // radio title
$date_format['txt_day'] = _('%a %e');
$date_format['txt_date'] = _('%Y-%m-%d');
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}
