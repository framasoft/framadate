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

// Change language when requested
if (isset($_REQUEST['lang'])
    && in_array($_REQUEST['lang'], array_keys($ALLOWED_LANGUAGES), true)) {
    $_SESSION['lang'] = $_REQUEST['lang'];
}

// Use the user-specified locale, or the browser-specified locale, or the app default.
$locale = $_SESSION['lang']
    ?: locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE'])
    ?: DEFAULT_LANGUAGE;

/* i18n helper functions */
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

class __i18n {
    private static $translator;
    private static $fallbacktranslator;
    
    public static function init($locale, $ALLOWED_LANGUAGES) {
        $found_locale = locale_lookup(array_keys($ALLOWED_LANGUAGES), $locale, false, DEFAULT_LANGUAGE);
        self::$translator = new Translator($found_locale);
        self::$translator->addLoader('pofile', new PoFileLoader());
        self::$translator->addResource('pofile', ROOT_DIR . "po/{$found_locale}.po", $found_locale);
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
__i18n::init($locale, $ALLOWED_LANGUAGES);

function __($section, $key) {
    return __i18n::translate($key);
}

function __f($section, $key, $args) {
    $msg = __i18n::translate($key);
    $args = array_slice(func_get_args(), 2);
    return vsprintf($msg, $args);
}

/* Date Format */
$date_format['txt_full'] = __('Date', '%A, %B %e, %Y'); //summary in create_date_poll.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = __('Date', '%A %e %B %Y'); // radio title
$date_format['txt_day'] = __('Date', '%a %e');
$date_format['txt_date'] = __('Date', '%Y-%m-%d');
$date_format['txt_month_year'] = __('Date', '%B %Y');
$date_format['txt_datetime_short'] = __('Date', '%m/%d/%Y %H:%M');
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}
