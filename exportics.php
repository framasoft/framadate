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


// TODO: no easy way to retrieve the best(s) choice(s) 
header('Location: studs.php');

$meilleursujet=$_SESSION["meilleursujet"];

session_start();
require_once('iCalcreator/iCalcreator.class.php');

$v = new vcalendar(); // create a new calendar instance
$v->setConfig( 'unique_id', $_SESSION["numsondage"] ); // set Your unique id
$v->setProperty( 'method', 'PUBLISH' ); // required of some calendar software
$vevent = new vevent(); // create an event calendar component

/*
  tested with :
  $test = array( '1275818164@12h-15h', '1275818164@12h15-15h57', '1275818164@12:15-15:57',  '1275818164@8:30',  '1275818164@8h30');
  foreach($test as $meilleursujet) {
*/
$adate = strtok($meilleursujet, "@");
$dtstart = $dtend = array(
  'year'=>intval(date("Y",$adate)),
  'month'=>intval(date("n",$adate)),
  'day'=>intval(date("j",$adate)),
  'hour'=>0,
  'min'=>0,
  'sec'=>0
);

$double_time = false;
if(strpos($meilleursujet, '-') !== false) {
  $double_time = true;
}

$dtstart['hour'] = intval(strtok(":Hh"));
$a = intval(strtok(":Hh-"));
$b = intval(strtok(":Hh-"));

if ($b === false) {
  if($double_time) {
    $dtend['hour'] = $a;
  } else {
    $dtstart['min'] = $a;
  }
} else {
  $dtstart['min'] = $a;
  $dtend['hour'] = $b;
  $dtend['min'] = intval(strtok(":Hh-"));
}

if(! $double_time ) {
  $dtend['hour'] = $dtstart['hour'] + 1;
  $dtend['min'] = $dtstart['min'];
}

$vevent->setProperty( 'dtstart', $dtstart);
$vevent->setProperty( 'dtend', $dtend);
$vevent->setProperty( 'summary', $_SESSION["sondagetitre"] );

$v->setComponent ( $vevent ); // add event to calendar
$v->setConfig( "language", "fr" );
$v->setConfig( "directory", "export" );
$v->setConfig( "filename", $_SESSION["numsondage"].".ics" ); // set file name
$v->returnCalendar(); 