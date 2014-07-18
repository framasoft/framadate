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
