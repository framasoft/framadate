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
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
        $port = in_array($_SERVER['SERVER_PORT'], [80, 443]) ? '' : ':' . $_SERVER['SERVER_PORT'];
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $dirname = $dirname === '\\' ? '/' : $dirname . '/';
        $server_name = $_SERVER['SERVER_NAME'] . $port . $dirname;

        return $scheme . '://' . str_replace('/admin', '', str_replace('//', '/', str_replace('///', '/', $server_name)));
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
        global $lang;

        echo '<!DOCTYPE html>
    <html lang="' . $lang . '">
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
        <script type="text/javascript" src="' . self::get_server_name() . 'js/locales/bootstrap-datepicker.' . $lang . '.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/core.js"></script>';
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/nav/nav.js")) {
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
     * Fonction permettant de générer les URL pour les sondage
     * @param   string $id L'identifiant du sondage
     * @param   bool $admin True pour générer une URL pour l'administration d'un sondage, False pour un URL publique
     * @return  string            L'url pour le sondage
     */
    public static function getUrlSondage($id, $admin = false) {
        if (URL_PROPRE) {
            if ($admin === true) {
                $url = str_replace('/admin', '', self::get_server_name()) . $id . '/admin';
            } else {
                $url = str_replace('/admin', '', self::get_server_name()) . $id;
            }
        } else {
            if ($admin === true) {
                $url = str_replace('/admin', '', self::get_server_name()) . 'adminstuds.php?poll=' . $id;
            } else {
                $url = str_replace('/admin', '', self::get_server_name()) . 'studs.php?poll=' . $id;
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

            $text = stripslashes($md_a_img[1][0]);
            $html = '<a href="' . $md_a_img[3][0] . '"><img src="' . $md_a_img[2][0] . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" /></a>';

        } elseif (isset($md_img[2][0]) && $md_img[2][0] != '') { // ![alt](src)

            $text = stripslashes($md_img[1][0]);
            $html = '<img src="' . $md_img[2][0] . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" />';

        } elseif (isset($md_a[2][0]) && $md_a[2][0] != '') { // [text](href)

            $text = stripslashes($md_a[1][0]);
            $html = '<a href="' . $md_a[2][0] . '">' . $text . '</a>';

        } else { // text only

            $text = stripslashes($md);
            $html = $text;

        }

        return $clear ? $text : $html;
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
}
