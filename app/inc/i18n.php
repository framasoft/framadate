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

const DATE_FORMAT_FULL = 'EEEE d MMMM y';
const DATE_FORMAT_SHORT = 'EEEE d MMMM y';
const DATE_FORMAT_DAY = 'E d';
const DATE_FORMAT_DATE = 'dd-MM-y';
const DATE_FORMAT_MONTH_YEAR = 'MMMM y';
const DATE_FORMAT_DATETIME_SHORT = 'EEEE d';

// Change session language when requested
if (isset($_REQUEST['lang'])
    && array_key_exists($_REQUEST['lang'], $ALLOWED_LANGUAGES)) {
    $_SESSION['lang'] = $_REQUEST['lang'];
}

// Try the user-specified locale, or the browser-specified locale.
if (isset($_SESSION['lang'])) {
    $wanted_locale = $_SESSION['lang'];
} else  {
    $wanted_locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
}
// Use the best available locale.
$locale = locale_lookup(array_keys($ALLOWED_LANGUAGES), $wanted_locale, false, DEFAULT_LANGUAGE);

/**
 * Formats a DateTime according to the IntlDateFormatter
 *
 * @param DateTime $date
 * @param string $pattern
 * @param $forceLocale
 * @return string
 */
function date_format_intl(DateTime $date, $pattern = DATE_FORMAT_FULL, $forceLocale = null) {
    global $locale;
    $local_locale = $forceLocale || $locale;

    $dateFormatter = IntlDateFormatter::create(
        $local_locale,
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        date_default_timezone_get(),
        IntlDateFormatter::GREGORIAN,
        $pattern
    );
    return $dateFormatter->format($date);
}

/**
 * Formats a DateTime according to a translated format
 *
 * @param DateTime $date
 * @param string $pattern
 * @return string
 */
function date_format_translation(DateTime $date, $pattern = 'Y-m-d') {
    return $date->format(__('Date', $pattern));
}

/**
 * Converts a string into a DateTime according to the IntlDateFormatter
 *
 * @param $dateString
 * @param string $pattern
 * @param string|null $forceLocale
 * @return DateTime|null
 */
function parse_intl_date($dateString, $pattern = DATE_FORMAT_DATE, $forceLocale = null) {
    global $locale;
    $local_locale = $forceLocale || $locale;

    $dateFormatter = IntlDateFormatter::create(
        $local_locale,
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        date_default_timezone_get(),
        IntlDateFormatter::GREGORIAN,
        $pattern
    );
    $timestamp = $dateFormatter->parse($dateString);
    try {
        return (new DateTime())->setTimestamp($timestamp);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Converts a string into a DateTime according to a translated format
 *
 * @param string $dateString
 * @param string $pattern
 * @return DateTime
 */
function parse_translation_date($dateString, $pattern = 'Y-m-d') {
    return DateTime::createFromFormat(__('Date', $pattern), $dateString);
}

/* i18n helper functions */
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

class __i18n {
    private static $translator;
    private static $fallbacktranslator;

    public static function init($locale) {
        self::$translator = new Translator($locale);
        self::$translator->addLoader('pofile', new PoFileLoader());
        self::$translator->addResource('pofile', ROOT_DIR . "po/{$locale}.po", $locale);
        # Fallback:
        # For Symfony/Translation, empty strings are valid, but in po files, untranslated strings are "".
        # This means we cannot use the standard $translator->setFallbackLocales() mechanism :(
        self::$fallbacktranslator = new Translator(DEFAULT_LANGUAGE);
        self::$fallbacktranslator->addLoader('pofile', new PoFileLoader());
        self::$fallbacktranslator->addResource('pofile', ROOT_DIR . "po/" . DEFAULT_LANGUAGE . ".po", DEFAULT_LANGUAGE);
    }

    public static function translate($key) {
        return self::$translator->trans($key)
            ?: self::$fallbacktranslator->trans($key);
    }
}
__i18n::init($locale);

function __($section, $key) {
    return __i18n::translate($key);
}

function __f($section, $key, $args) {
    $msg = __i18n::translate($key);
    $args = array_slice(func_get_args(), 2);
    return vsprintf($msg, $args);
}
