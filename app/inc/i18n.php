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

// Prepare I18N instance
$i18n = \o80\i18n\I18N::instance();
$i18n->setDefaultLang(DEFAULT_LANGUAGE);
$i18n->setPath(__DIR__ . '/../../locale');

// Change langauge when user asked for it
if (isset($_POST['lang']) && is_string($_POST['lang']) && in_array($_POST['lang'], array_keys($ALLOWED_LANGUAGES))) {
    $_SESSION['lang'] = $_POST['lang'];
}

/* <html lang="$locale"> */
$i18n->get('', 'Something, just to load the dictionary');
$locale = $i18n->getLoadedLang();

/* Date Format */
$date_format['txt_full'] = __('Date', 'FULL'); //summary in create_date_poll.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = __('Date', 'SHORT'); // radio title
$date_format['txt_day'] = __('Date', 'DAY');
$date_format['txt_date'] = __('Date', 'DATE');
$date_format['txt_month_year'] = __('Date', 'MONTH_YEAR');
if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}
