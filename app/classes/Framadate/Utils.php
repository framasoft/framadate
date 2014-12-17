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

class Utils
{
    public static function get_server_name()
    {
        $scheme = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
        $port = in_array($_SERVER['SERVER_PORT'], [80, 443]) ? '' : ':' . $_SERVER['SERVER_PORT'];
        $dirname = dirname($_SERVER['SCRIPT_NAME']);
        $dirname = $dirname === '\\' ? '/' : $dirname . '/';
        $server_name = $_SERVER['SERVER_NAME'] . $port . $dirname;
        return $scheme . '://' .  str_replace('/admin','',str_replace('//','/',str_replace('///','/',$server_name)));
    }

    public static function is_error($cerr)
    {
        global $err;
        if ($err == 0) {
            return false;
        }

        return ($err & $cerr) != 0;
    }

    public static function is_user()
    {
        return (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) || isset($_SESSION['nom']);
    }

    public static function print_header($title = '')
    {
        global $lang;

        echo '<!DOCTYPE html>
    <html lang="'.$lang.'">
    <head>
        <meta charset="utf-8">';

        if (! empty($title)) {
            echo '<title>' . stripslashes($title) . ' - ' . NOMAPPLICATION . '</title>';
        } else {
            echo '<title>' . NOMAPPLICATION . '</title>';
        }

        echo '
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/bootstrap.min.css">
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/datepicker3.css">
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/style.css">
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/frama.css">
        <link rel="stylesheet" href="' . self::get_server_name() . 'css/print.css" media="print">
        <script type="text/javascript" src="' . self::get_server_name() . 'js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap.min.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/locales/bootstrap-datepicker.'.$lang.'.js"></script>
        <script type="text/javascript" src="' . self::get_server_name() . 'js/core.js"></script>';
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/nav/nav.js")) {
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
     * @param   string  $email  Email address to check
     * @return  bool    True if valid. False if not valid.
     */
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Envoi un courrier avec un codage correct de To et Subject
     * Les en-têtes complémentaires ne sont pas gérés
     *
     */
    public static function sendEmail( $to, $subject, $body, $headers='', $param='')
    {

        mb_internal_encoding('UTF-8');

        $subject = mb_encode_mimeheader(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'), 'UTF-8', 'B', "\n", 9);

        $encoded_app = mb_encode_mimeheader(NOMAPPLICATION, 'UTF-8', 'B', "\n", 6);
        $size_encoded_app = (6 + strlen($encoded_app)) % 75;
        $size_admin_email = strlen(ADRESSEMAILADMIN);

        if (($size_encoded_app + $size_admin_email + 9) > 74 ) {
            $folding = "\n";
        } else {
            $folding = '';
        };

        /*
           Si $headers ne contient qu'une adresse email, on la considère comme
           adresse de reply-to, sinon on met l'adresse de no-reply definie
           dans constants.php
        */
        if (self::isValidEmail($headers)) {
            $replyTo = $headers;
            $headers = ''; // on reinitialise $headers
        } else {
            $replyTo = ADRESSEMAILREPONSEAUTO;
        }

        $from = sprintf( "From: %s%s <%s>\n", $encoded_app, $folding, ADRESSEMAILADMIN);

        if ($headers) {
            $headers .= "\n" ;
        }

        $headers .= $from;
        $headers .= "Reply-To: $replyTo\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\n";
        $headers .= "Content-Transfer-Encoding: 8bit\n";
        $headers .= "Auto-Submitted:auto-generated\n";
        $headers .= "Return-Path: <>";

        $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8')._("\n--\n\n« La route est longue, mais la voie est libre… »\nFramasoft ne vit que par vos dons (déductibles des impôts).\nMerci d'avance pour votre soutien http://soutenir.framasoft.org.");

        mail($to, $subject, $body, $headers, $param);
    }

    /**
     * Fonction permettant de générer les URL pour les sondage
     * @param   string    $id     L'identifiant du sondage
     * @param   bool      $admin  True pour générer une URL pour l'administration d'un sondage, False pour un URL publique
     * @return  string            L'url pour le sondage
     */
    public static function getUrlSondage($id, $admin = false)
    {
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
     * Completly delete data about the given poll
     * TODO Move this function to FramaDB
     */
    public static function removeSondage($poll_id)
    {
        global $connect;

        $prepared = $connect->prepare('DELETE FROM sujet_studs WHERE id_sondage = ?');
        $prepared->execute(array($poll_id));

        $prepared = $connect->prepare('DELETE FROM user_studs WHERE id_sondage = ?');
        $prepared->execute(array($poll_id));

        $prepared = $connect->prepare('DELETE FROM comments WHERE id_sondage = ?');
        $prepared->execute(array($poll_id));

        $prepared = $connect->prepare('DELETE FROM sondage WHERE poll_id = ?');
        $prepared->execute(array($poll_id));

    }

    /**
     * Clean old poll (end_date < now).
     * TODO Move this function to FramaDB
     */
    public static function cleaningOldPolls($log_txt) {
        global $connect;

        $resultSet = $connect->query('SELECT poll_id, format, admin_name FROM sondage WHERE end_date < NOW() LIMIT 20');
        $toClean = $resultSet->fetchAll(\PDO::FETCH_CLASS);

        $connect->beginTransaction();
        foreach ($toClean as $row) {
            if (self::removeSondage($row->poll_id)) {
                error_log(date('H:i:s d/m/Y:') . ' EXPIRATION: '. $row->poll_id."\t".$row->format."\t".$row->admin_name."\n", 3, $log_txt);
            }
        }
        $connect->commit();
    }

    /**
     * This method pretty prints an object to the page framed by pre tags.
     * @param Object $object The object to print.
     */
    public static function debug($object)
    {
        echo '<pre>';
        print_r($object);
        echo '</pre>';
    }
}
