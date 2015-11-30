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
namespace Framadate;

class Utils {
    /**
     * @return string Server name
     */
    public static function get_server_name() {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $port = in_array($_SERVER['SERVER_PORT'], [80, 443]) ? '' : ':' . $_SERVER['SERVER_PORT'];
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $dirname = $dirname === '\\' ? '/' : $dirname . '/';
        $dirname = str_replace('/admin', '', $dirname);
        $server_name = (defined('APP_URL') ? APP_URL : $_SERVER['SERVER_NAME']) . $port . $dirname;

        return $scheme . '://' . preg_replace('#//+#', '/', $server_name);
    }

    public static function is_error($cerr) {
        global $err;
        if ($err == 0) {
            return false;
        }

        return ($err & $cerr) != 0;
    }

    public static function is_user() {
        return (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) || isset($_SESSION['nom']);
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
        <script type="text/javascript" src="' . self::get_server_name() . 'js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/locales/bootstrap-datepicker.' . $locale . '.js"></script>
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
     * Check if an email address is valid using PHP filters
     *
     * @param   string $email Email address to check
     * @return  bool    True if valid. False if not valid.
     * @deprecated
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Function allowing to generate poll's url
     * @param   string $id The poll's id
     * @param   bool $admin True to generate an admin URL, false for a public one
     * @param   string $vote_id (optional) The vote's unique id
     * @return  string The poll's URL.
     */
    public static function getUrlSondage($id, $admin = false, $vote_id = '', $action = null, $action_value = null) {
        // URL-Encode $action_value
        $action_value = $action_value == null ? null : Utils::base64url_encode($action_value);

        if (URL_PROPRE) {
            if ($admin === true) {
                $url = self::get_server_name() . $id . '/admin';
            } else {
                $url = self::get_server_name() . $id;
            }
            if ($vote_id != '') {
                $url .= '/vote/' . $vote_id . "#edit";
            } elseif ($action != null) {
                if ($action_value != null) {
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
            if ($vote_id != '') {
                $url .= '&vote=' . $vote_id . "#edit";
            } elseif ($action != null)  {
                if ($action_value != null) {
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

    public static function markdown($md, $clear) {
        preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $md, $md_a_img); // Markdown [![alt](src)](href)
        preg_match_all('/!\[(.*?)\]\((.*?)\)/', $md, $md_img); // Markdown ![alt](src)
        preg_match_all('/\[(.*?)\]\((.*?)\)/', $md, $md_a); // Markdown [text](href)
        if (isset($md_a_img[2][0]) && $md_a_img[2][0] != '' && isset($md_a_img[3][0]) && $md_a_img[3][0] != '') { // [![alt](src)](href)

            $text = self::htmlEscape($md_a_img[1][0]);
            $html = '<a href="' . self::htmlEscape($md_a_img[3][0]) . '"><img src="' . self::htmlEscape($md_a_img[2][0]) . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" /></a>';

        } elseif (isset($md_img[2][0]) && $md_img[2][0] != '') { // ![alt](src)

            $text = self::htmlEscape($md_img[1][0]);
            $html = '<img src="' . self::htmlEscape($md_img[2][0]) . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" />';

        } elseif (isset($md_a[2][0]) && $md_a[2][0] != '') { // [text](href)

            $text = self::htmlEscape($md_a[1][0]);
            $html = '<a href="' . $md_a[2][0] . '">' . $text . '</a>';

        } else { // text only

            $text = self::htmlEscape($md);
            $html = $text;

        }

        return $clear ? $text : $html;
    }

    public static function htmlEscape($html) {
        return htmlentities($html, ENT_HTML5 | ENT_QUOTES);
    }

    public static function csvEscape($text) {
        $escaped = str_replace('"', '""', $text);
        $escaped = str_replace("\r\n", '', $escaped);
        $escaped = str_replace("\n", '', $escaped);

        return '"' . $escaped . '"';
    }

    public static function cleanFilename($title) {
        $cleaned = preg_replace('[^a-zA-Z0-9._-]', '_', $title);
        $cleaned = preg_replace(' {2,}', ' ', $cleaned);

        return $cleaned;
    }

    public static function fromPostOrDefault($postKey, $default = '') {
        return !empty($_POST[$postKey]) ? Utils::htmlEscape($_POST[$postKey]) : $default;
    }

    public static function base64url_encode($input) {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    public static function base64url_decode($input) {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    }
}
