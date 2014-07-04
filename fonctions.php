<?php
/* This software is governed by the CeCILL-B license. If a copy of this license 
 * is not distributed with this file, you can obtain one at 
 * http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 * 
 * Authors of STUdS (initial project) : Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of OpenSondage : Framasoft (https://github.com/framasoft)
 * 
 * =============================
 * 
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence 
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur 
 * http://www.cecill.info/licences/Licence_CeCILL_V2.1-fr.txt
 * 
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs d'OpenSondage : Framasoft (https://github.com/framasoft)
 */
 
if(ini_get('date.timezone') == '') {
  date_default_timezone_set("Europe/Paris");
}

include_once('variables.php');
include_once('i18n.php');
require_once('adodb/adodb.inc.php');

function connexion_base()
{
  $DB = NewADOConnection(BASE_TYPE);
  $DB->Connect(SERVEURBASE, USERBASE, USERPASSWD, BASE);
  //$DB->debug = true;
  return $DB;
}

function get_server_name()
{
    $scheme = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
    $port = in_array($_SERVER['SERVER_PORT'], array(80, 443)) ? '/' : ':' . $_SERVER['SERVER_PORT'] . '/';
    $server_name = $_SERVER['SERVER_NAME'] . $port . dirname($_SERVER['SCRIPT_NAME']).'/';
    return $scheme . '://' .  str_replace("///","/",$server_name);
}

function get_sondage_from_id($id)
{
  global $connect;

  // Ouverture de la base de données
  if(preg_match(";^[\w\d]{16}$;i",$id)) {
    $sql = 'SELECT sondage.*,sujet_studs.sujet FROM sondage
            LEFT OUTER JOIN sujet_studs ON sondage.id_sondage = sujet_studs.id_sondage
            WHERE sondage.id_sondage = '.$connect->Param('id_sondage');

    $sql = $connect->Prepare($sql);
    $sondage=$connect->Execute($sql, array($id));

    if ($sondage === false) {
      return false;
    }

    $psondage = $sondage->FetchObject(false);
    $psondage->date_fin = strtotime($psondage->date_fin);
    return $psondage;
  }

  return false;
}


function is_error($cerr)
{
  global $err;
  if ( $err == 0 ) {
    return false;
  }

  return (($err & $cerr) != 0 );
}


function is_user()
{
  return ( USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']) ) || (isset($_SESSION['nom']));
}


function print_header($js = false, $nom_sondage = '', $lang = 'fr')
{
  echo '<!DOCTYPE html>
<html lang="'.$lang.'">
  <head>
    <meta charset="utf-8">';
  if (empty($nom_sondage) === false) {
    echo '
    <title>'.stripslashes($nom_sondage).' - '.NOMAPPLICATION.'</title>';
  } else {
    echo '
    <title>'.NOMAPPLICATION.'</title>';
  }
  echo '
    <link rel="stylesheet" href="'.get_server_name().'style.css">
    <link rel="stylesheet" href="'.get_server_name().'print.css" media="print">';

  echo '</head>';
}


function check_table_sondage()
{
  global $connect;
  $tables = $connect->MetaTables('TABLES');
  if (in_array("sondage", $tables)) {
    return true;
  }
  return false;
}


/**
 * Vérifie une adresse e-mail selon les normes RFC
 * @param  string  $email  l'adresse e-mail a vérifier
 * @return  bool    vrai si l'adresse est correcte, faux sinon
 * @see http://fightingforalostcause.net/misc/2006/compare-email-regex.php
 * @see http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c?view=markup
 */
function validateEmail($email)
{
  $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

  return (bool)preg_match($pattern, $email);
}

# Envoi un courrier avec un codage correct de To et Subject
# Les en-têtes complémentaires ne sont pas gérés

function sendEmail( $to, $subject, $body, $headers, $param)
{

  mb_internal_encoding("UTF-8");

  $subject = mb_encode_mimeheader( html_entity_decode( $subject, ENT_QUOTES, 'UTF-8' ), "UTF-8", "B", "\n", 9 ) ;

  $encoded_app = mb_encode_mimeheader( NOMAPPLICATION, "UTF-8", "B", "\n", 6 ) ;
  $size_encoded_app = ( 6 + strlen( $encoded_app ) ) % 75 ;
  $size_admin_email = strlen( ADRESSEMAILADMIN ) ;

  if ( ( $size_encoded_app + $size_admin_email + 9 ) > 74 ) {
    $folding = "\n" ;
  } else {
    $folding = "" ;
  } ;
  // si $headers ne contient qu'une adresse email, on la considère comme adresse de reply-to, sinon on met l'adresse de no-reply definie dans variables.php
  if (validateEmail($headers)) {
    $replyTo = $headers;
    $headers = ""; // on reinitialise $headers
  } else {
    $replyTo = ADRESSEEMAILREPONSEAUTO;
  }
  
  
  $from = sprintf( "From: %s%s <%s>\n", $encoded_app, $folding, ADRESSEMAILADMIN ) ;

  if ( $headers ) $headers .= "\n" ;
  $headers .= $from ;
  $headers .= "Reply-To: $replyTo\n";
  $headers .= "MIME-Version: 1.0\n" ;
  $headers .= "Content-Type: text/plain; charset=UTF-8\n" ;
  $headers .= "Content-Transfer-Encoding: 8bit" ;

  $body = html_entity_decode( $body, ENT_QUOTES, 'UTF-8' ) ;

  mail( $to, $subject, $body, $headers, $param ) ;

}


/**
 * Fonction vérifiant l'existance et la valeur non vide d'une clé d'un tableau
 * @param   string  $name       La clé à tester
 * @param   array   $tableau    Le tableau où rechercher la clé ($_POST par défaut)
 * @return  bool                Vrai si la clé existe et renvoie une valeur non vide
 */
function issetAndNoEmpty($name, $tableau = null)
{
  if ($tableau === null) {
    $tableau = $_POST;
  }

  return (isset($tableau[$name]) === true && empty($tableau[$name]) === false);
}


/**
 * Fonction permettant de générer les URL pour les sondage
 * @param   string    $id     L'identifiant du sondage
 * @param   bool      $admin  True pour générer une URL pour l'administration d'un sondage, False pour un URL publique
 * @return  string            L'url pour le sondage
 */
function getUrlSondage($id, $admin = false)
{
  if (URL_PROPRE === true) {
    if ($admin === true) {
      $url = str_replace('/admin','',get_server_name()).$id.'/admin';
    } else {
      $url = str_replace('/admin','',get_server_name()).$id;
    }
  } else {
    if ($admin === true) {
      $url = str_replace('/admin','',get_server_name()).'adminstuds.php?sondage='.$id;
    } else {
      $url = str_replace('/admin','',get_server_name()).'studs.php?sondage='.$id;
    }
  }

  return $url;
}

function remove_sondage( $connect, $numsondage ){

  $connect->StartTrans();

  $req = 'DELETE FROM sondage     WHERE id_sondage = ' . $connect->Param('numsondage') ;
  $sql = $connect->Prepare($req);
  $connect->Execute($sql, array($numsondage));

  $req = 'DELETE FROM sujet_studs WHERE id_sondage = ' . $connect->Param('numsondage') ;
  $sql = $connect->Prepare($req);
  $connect->Execute($sql, array($numsondage));

  $req = 'DELETE FROM user_studs  WHERE id_sondage = ' . $connect->Param('numsondage') ;
  $sql = $connect->Prepare($req);
  $connect->Execute($sql, array($numsondage));

  $req = 'DELETE FROM comments    WHERE id_sondage = ' . $connect->Param('numsondage') ;
  $sql = $connect->Prepare($req);
  $connect->Execute($sql, array( $numsondage ));

  $suppression_OK = ! $connect->HasFailedTrans() ;
  $connect->CompleteTrans();

  return $suppression_OK ;

}


$connect=connexion_base();

define('COMMENT_EMPTY',         0x0000000001);
define('COMMENT_USER_EMPTY',    0x0000000010);
define('COMMENT_INSERT_FAILED', 0x0000000100);
define('NAME_EMPTY',            0x0000001000);
define('NAME_TAKEN',            0x0000010000);
define('NO_POLL',               0x0000100000);
define('NO_POLL_ID',            0x0001000000);
define('INVALID_EMAIL',         0x0010000000);
define('TITLE_EMPTY',           0x0100000000);
define('INVALID_DATE',          0x1000000000);
$err = 0;
