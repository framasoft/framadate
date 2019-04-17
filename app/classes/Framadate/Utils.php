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

use Parsedown;

class Utils {
    /**
     * @return string Server name
     */
    public static function get_server_name() {
        $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
        $serverPort = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '';

        $scheme = (
            (defined('FORCE_HTTPS') && FORCE_HTTPS ) ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ) ? 'https' : 'http';

        $domain = defined('APP_URL') ? APP_URL : $serverName;
        $port = in_array($serverPort, ['80', '443'], true) ? '' : ':' . $serverPort;
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $dirname = $dirname === '\\' ? '/' : $dirname . '/';
        $dirname = str_replace('/admin', '', $dirname);
        $dirname = str_replace('/action', '', $dirname);

        return $scheme . '://' . preg_replace('#//+#', '/', $domain . $port . $dirname);
    }

    /**
     * @param string $title
     * @deprecated
     */
    public static function print_header($title = '') {
        global $locale;

        echo '<!DOCTYPE html>
    <html lang="' . $locale . '">
    <head>
        <meta charset="utf-8" />';

        if (!empty($title)) {
            echo '<title>' . stripslashes($title) . ' - ' . NOMAPPLICATION . '</title>';
        } else {
            echo '<title>' . NOMAPPLICATION . '</title>';
        }

        echo '
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/bootstrap.min.css" />
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/datepicker3.css" />
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/style.css" />
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/frama.css" />
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/print.css" media="print" />
        <script type="text/javascript" src="' . self::get_server_name() . 'js/jquery-1.12.4.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap-datepicker.js"></script>';
        if ('en' !== $locale) {
        	   echo '
        <script type="text/javascript" src="' . self::get_server_name() . 'js/locales/bootstrap-datepicker.' . $locale . '.js"></script>';
        }
        echo '
        <script type="text/javascript" src="' . self::get_server_name() . 'js/core.js"></script>';
        if (is_file($_SERVER['DOCUMENT_ROOT'] . "/nav/nav.js")) {
            echo '<script src="/nav/nav.js" id="nav_js" type="text/javascript" charset="utf-8"></script><!-- /Framanav -->';
        }

        echo '
    </head>
    <body>
    <div class="container ombre">';
    }

    /**
     * Function allowing to generate poll's url
     * @param   string $id The poll's id
     * @param   bool $admin True to generate an admin URL, false for a public one
     * @param   string $vote_id (optional) The vote's unique id
     * @param null $action
     * @param null $action_value
     * @return  string The poll's URL.
     */
    public static function getUrlSondage($id, $admin = false, $vote_id = '', $action = null, $action_value = null) {
        // URL-Encode $action_value
        $action_value = $action_value ? Utils::base64url_encode($action_value) : null;

        if (URL_PROPRE) {
            if ($admin === true) {
                $url = self::get_server_name() . $id . '/admin';
            } else {
                $url = self::get_server_name() . $id;
            }
            if ($vote_id !== '') {
                $url .= '/vote/' . $vote_id . "#edit";
            } elseif ($action) {
                if ($action_value) {
                    $url .= '/action/' . $action . '/' . $action_value;
                } else {
                    $url .= '/action/' . $action;
                }
            }
        } else {
            if ($admin === true) {
                $url = self::get_server_name() . 'adminstuds.php?poll=' . $id;
            } else {
                $url = self::get_server_name() . 'studs.php?poll=' . $id;
            }
            if ($vote_id !== '') {
                $url .= '&vote=' . $vote_id . "#edit";
            } elseif ($action)  {
                if ($action_value) {
                    $url .= '&' . $action . "=" . $action_value;
                } else {
                    $url .= '&' . $action . "=";
                }
            }
        }

        return $url;
    }

    /**
     * This method pretty prints an object to the page framed by pre tags.
     *
     * @param mixed $object The object to print.
     */
    public static function debug($object) {
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }

    public static function table($tableName) {
        return TABLENAME_PREFIX . $tableName;
    }

    public static function markdown($md, $clear=false, $line=true) {
        $parseDown = new Parsedown();

        $parseDown
            ->setBreaksEnabled(true)
            ->setSafeMode(true)
            ;

        if ($line) {
            $html  = $parseDown->line($md);
        } else {
            $md = preg_replace_callback(
                '#( ){2,}#',
                function ($m) {
                    return str_repeat('&nbsp;', strlen($m[0]));
                },
                $md
            );
            $html  = $parseDown->text($md);
        }

        $text = strip_tags($html);

        return $clear ? $text : $html;
    }

    public static function htmlEscape($html) {
        return htmlentities($html, ENT_HTML5 | ENT_QUOTES);
    }

    public static function htmlMailEscape($html) {
        return htmlspecialchars($html, ENT_HTML5 | ENT_QUOTES);
    }

    public static function csvEscape($text) {
        $escaped = str_replace('"', '""', $text);
        $escaped = str_replace("\r\n", '', $escaped);
        $escaped = str_replace("\n", '', $escaped);
        $escaped = preg_replace("/^(=|\+|\-|\@)/", "'$1", $escaped);

        return '"' . $escaped . '"';
    }

    public static function cleanFilename($title) {
        $cleaned = preg_replace('[^a-zA-Z0-9._-]', '_', $title);
        $cleaned = preg_replace(' {2,}', ' ', $cleaned);

        return $cleaned;
    }

    public static function fromPostOrDefault($postKey, $default = '') {
        return !empty($_POST[$postKey]) ? $_POST[$postKey] : $default;
    }

    public static function base64url_encode($input) {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    public static function base64url_decode($input) {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT), true);
    }
}
