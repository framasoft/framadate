<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use, 
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info". 
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================

if(ini_get('date.timezone') == '')
  date_default_timezone_set("Europe/Paris");

include_once('variables.php');
include_once('i18n.php');
require_once('adodb/adodb.inc.php');

function connexion_base(){
       $DB = NewADOConnection(BASE_TYPE);
       $DB->Connect(SERVEURBASE, USERBASE, USERPASSWD, BASE);
       //$DB->debug = true;
       return $DB;
}

function get_server_name() {
  $scheme = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? 'https' : 'http';
	$url = sprintf("%s://%s%s", $scheme,
		      STUDS_URL,
		      dirname($_SERVER["SCRIPT_NAME"]));
	if (!preg_match("|/$|", $url)){
		$url = $url."/";        
	}
	return $url;
}

function get_sondage_from_id($id) {
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

function is_error($cerr) {
  global $err;
  if ( $err == 0 )
    return false;
  return (($err & $cerr) != 0 );
}


function is_user() {
  return isset($_SERVER['REMOTE_USER']) || (isset($_SESSION['nom']));
}

function print_header($js = false) {
  ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo NOMAPPLICATION; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
<?php
if($js)
  echo '<script type="text/javascript" src="block_enter.js"></script>';
echo '</head>';
}

function check_table_sondage() {
  global $connect;
  $tables = $connect->MetaTables('TABLES');
  if (in_array("sondage", $tables))
    return true;
  return false;
}



/**
 * Vérifie une adresse e-mail selon les normes RFC
 * @param	string	$email	l'adresse e-mail a vérifier
 * @return	bool		vrai si l'adresse est correcte, faux sinon
 * @see http://fightingforalostcause.net/misc/2006/compare-email-regex.php
 * @see http://svn.php.net/viewvc/php/php-src/trunk/ext/filter/logical_filters.c?view=markup
 */
function validateEmail($email) {
  $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
  
  return (bool)preg_match($pattern, $email);
}

?>
