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

// Sort languages
asort($ALLOWED_LANGUAGES);

// Prepare I18N instance
$i18n = \o80\i18n\I18N::instance();
$i18n->setDefaultLang(DEFAULT_LANGUAGE);
$i18n->setPath(__DIR__ . '/../../locale');

// Change langauge when user asked for it
if (isset($_POST['lang']) && is_string($_POST['lang']) && in_array($_POST['lang'], array_keys($ALLOWED_LANGUAGES))) {
    $locale = $_POST['lang'];
    $_SESSION['lang'] = $_POST['lang'];
} elseif (!empty($_SESSION['lang'])) {
    $locale = $_SESSION['lang'];
} else {
    $locale = DEFAULT_LANGUAGE;
}

/* <html lang="$html_lang"> */
$html_lang = substr($locale, 0, 2);

/* Date Format */
$date_format['txt_full'] = _('%A, den %e. %B %Y'); //summary in choix_date.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = _('%A %e %B %Y'); // radio title
$date_format['txt_day'] = _('%a %e');
$date_format['txt_date'] = _('%Y-%m-%d');
$date_format['txt_year_month'] = _('%B %Y');
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}
