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

include_once __DIR__ . '/app/inc/init.php';

if(!isset($_GET['numsondage']) || ! preg_match(";^[\w\d]{16}$;i", $_GET['numsondage'])) {
    header('Location: studs.php');
}

$sql = 'SELECT * FROM user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($_GET['numsondage']));

$dsondage = Utils::get_sondage_from_id($_GET['numsondage']);

$content = chr(239) . chr(187) . chr(191) ; //UTF-8 BOM
$content .= _("Title of the poll") ." : ".stripslashes($dsondage->titre).PHP_EOL;
$content .= _("Initiator of the poll") ." : ".stripslashes($dsondage->nom_admin).PHP_EOL;
$content .= _("Description") ." : ".stripslashes($dsondage->commentaires).PHP_EOL;
$content .= _("Public link of the poll") ." : ".Utils::getUrlSondage($dsondage->id_sondage).PHP_EOL ;
$content .= _("Admin link of the poll") ." : ".Utils::getUrlSondage($dsondage->id_sondage_admin,true).PHP_EOL;
$content .= _("Expiration's date") ." : ".date("d/m/Y",$dsondage->date_fin);
$filesize = strlen( $content );
$filename=$_GET["numsondage"].".txt";

header( 'Content-Type: text/plain; charset=utf-8' );
header( 'Content-Length: '.$filesize );
header( 'Content-Disposition: attachment; filename="'.$filename.'"' );
header( 'Cache-Control: max-age=10' );

//echo str_replace('&quot;','""',$content);
$content = html_entity_decode($content);
echo $content;
die();