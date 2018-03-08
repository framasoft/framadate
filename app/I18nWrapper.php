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

namespace Framadate;

use o80\i18n\CantLoadDictionaryException;
use o80\i18n\I18N;

class I18nWrapper {

    /**
     * @var I18N
     */
    private $i18n;

    /**
     * @var string
     */
    private $locale = DEFAULT_LANGUAGE;

    public function __construct()
    {
        // Prepare I18N instance
        $this->i18n = I18N::instance();
        $this->i18n->setDefaultLang($this->locale);
        $this->i18n->setPath(__DIR__ . '/../locale');
        $this->i18n->get('', 'Something, just to load the dictionary');
    }

    /**
     * @param $section
     * @param $key
     * @param array $args
     * @return string
     */
    public function get($section, $key, $args = [])
    {
        try {
            if ($args !== []) {
                return $this->i18n->get($section, $key);
            }
            return $this->i18n->format($section, $key, $args);
        } catch (CantLoadDictionaryException $e) {
            // log exception
            return 'CantLoadDictionaryException';
        }
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->i18n->getLoadedLang();
    }

    /**
     * @param string $lang
     * @return I18nWrapper
     */
    public function setLocale($lang)
    {
        if (in_array($lang, array_keys(ALLOWED_LANGUAGES), true)) {
            $this->locale = $lang;
        }
        return $this;
    }
}

/* <html lang="$locale"> */

/* Date Format */
/*$date_format['txt_full'] = __('Date', 'FULL'); //summary in create_date_poll.php and removal date in choix_(date|autre).php
$date_format['txt_short'] = __('Date', 'SHORT'); // radio title
$date_format['txt_day'] = __('Date', 'DAY');
$date_format['txt_date'] = __('Date', 'DATE');
$date_format['txt_month_year'] = __('Date', 'MONTH_YEAR');
$date_format['txt_datetime_short'] = __('Date', 'DATETIME');
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') { //%e can't be used on Windows platform, use %#d instead
    foreach ($date_format as $k => $v) {
        $date_format[$k] = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $v); //replace %e by %#d for windows
    }
}*/
