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
session_start();

require_once('php2pdf/phpToPDF.php');
include_once('fonctions.php');

$dsondage = get_sondage_from_id($_POST['numsondage']);
$lieureunion=stripcslashes($_POST["lieureunion"]);
$datereunion=explode("@",$_POST["meilleursujet"]);

//creation du fichier PDF
$PDF=new phpToPDF();
$PDF->AddPage();
$PDF->SetFont('Arial','',11);

//affichage de la date de convocation
$PDF->Text(140,30,"Le ".date("d/m/Y"));

$PDF->Image("./".LOGOLETTRE."",20,20,65,40);

$PDF->SetFont('Arial','U',11);
$PDF->Text(40,120,"Objet : ");
$PDF->SetFont('Arial','',11);
$PDF->Text(55,120,_(' Convocation'));

$PDF->Text(55,140,_('Hello,'));

$PDF->Text(40,150,_("You're invited at the meeting") . ' "'.utf8_decode($dsondage->titre).'".');

$PDF->SetFont('Arial','B',11);
$PDF->Text(40,170,_('Informations about the meeting'));

$PDF->SetFont('Arial','',11);
$PDF->Text(60,180,_('Date') . ' : '.date("d/m/Y", "$datereunion[0]").' ' . _('at') . ' '.$datereunion[1]);
$PDF->Text(60,185,_('Place') . ' : ' . utf8_decode($lieureunion));

$PDF->Text(55,220,_('Cordially,'));

$PDF->Text(140,240,utf8_decode($dsondage->nom_admin));

$PDF->SetFont('Arial','B',8);
// TODO: translate
$PDF->Text(35,275,"Cette lettre de convocation a été générée automatiquement par ".NOMAPPLICATION." sur ". $_SERVER['SERVER_NAME']);

//Sortie
$PDF->Output();
