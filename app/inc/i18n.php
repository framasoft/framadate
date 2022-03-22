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

// Prepare I18N instance
use o80\i18n\I18N;

$i18n = I18N::instance();
$i18n->setDefaultLang(DEFAULT_LANGUAGE);
$i18n->setPath(__DIR__ . '/../../locale');

// Change language when user asked for it
if (isset($_POST['lang']) && is_string($_POST['lang']) && array_key_exists($_POST['lang'], $ALLOWED_LANGUAGES)) {
    $_SESSION['lang'] = $_POST['lang'];
}

/* <html lang="$locale"> */
$i18n->get('', 'Something, just to load the dictionary');
$locale = str_replace('_', '-', $i18n->getLoadedLang());

/* Date Format */
//$date_format['txt_full'] = __('Date', 'FULL'); //summary in create_date_poll.php and removal date in choix_(date|autre).php
//$date_format['txt_short'] = __('Date', 'SHORT'); // radio title
//$date_format['txt_day'] = __('Date', 'DAY');
//$date_format['txt_date'] = __('Date', 'DATE');
//$date_format['txt_month_year'] = __('Date', 'MONTH_YEAR');
//$date_format['txt_datetime_short'] = __('Date', 'DATETIME');

$date_format['txt_full'] = 'eeee d MMMM y'; //summary in create_date_poll.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = 'E d MMMM y'; // radio title
$date_format['txt_day'] = 'E d';
$date_format['txt_date'] = ''; // Defaults to IntlDateFormatter::SHORT
$date_format['txt_month_year'] = 'MMMM y';
$date_format['txt_datetime_short'] = 'dd-MM-y HH:mm';

function formatDate(string $format, $date) {
    global $locale;
    $locales = ResourceBundle::getLocales('');
    if (!in_array($locale, $locales, true)) {
        $locale = DEFAULT_LANGUAGE;
    }
    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::SHORT, IntlDateFormatter::NONE);
    if ($format !== '') {
        $formatter->setPattern($format);
    }
    if (is_numeric($date)) {
        $date = (new DateTime())->setTimestamp($date);
    } elseif (gettype($date) === 'string') {
        $date = new DateTime($date);
    }
    return datefmt_format($formatter, $date);
}

if (PHP_OS_FAMILY === 'Windows') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}
