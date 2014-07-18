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



session_start();

include_once('../variables.php');
include_once('../fonctions.php');
include_once('../bandeaux.php');

// Ce fichier index.php se trouve dans le sous-repertoire ADMIN de Studs. Il sert à afficher l'intranet de studs 
// pour modifier les sondages directement sans avoir reçu les mails. C'est l'interface d'aministration
// de l'application.

// Affichage des balises standards
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
echo '<html lang="'.$lang.'">'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>'._("Polls administrator").' - '.NOMAPPLICATION.'</title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'../style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

//Affichage des bandeaux et début du formulaire
framanav();
logo();
bandeau_tete();
bandeau_titre(_("Polls administrator"));
sous_bandeau_admin();
//print_r($_SESSION);

$sondage=$connect->Execute("select * from sondage");

echo'<div class=corps>'."\n";
echo '<form action="'.get_server_name().'index.php" method="POST">'."\n";
// Test et affichage du bouton de confirmation en cas de suppression de sondage
while($dsondage = $sondage->FetchNextObject(false)) {
  if (issetAndNoEmpty('supprimersondage'.$dsondage->id_sondage) === true) {
    echo '<table>'."\n";
    echo '<tr><td bgcolor="#EE0000" colspan="11">'. _("Confirm removal of the poll ") .'"'.$dsondage->id_sondage.'" : <input type="submit" name="confirmesuppression'.$dsondage->id_sondage.'" value="'. _("Remove this poll!") .'">'."\n";
    echo '<input type="submit" name="annullesuppression" value="'. _("Keep this poll!") .'"></td></tr>'."\n";
    echo '</table>'."\n";
    echo '<br>'."\n";
  }
  
  // Traitement de la confirmation de suppression
  if (issetAndNoEmpty('confirmesuppression'.$dsondage->id_sondage) === true) {
    // On inclut la routine de suppression
    $date=date('H:i:s d/m/Y');

    if ( remove_sondage( $connect, $dsondage->id_sondage ) ) {

      // ecriture des traces dans le fichier de logs
      error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'logs_studs.txt');

    }

  }

}

$sondage=$connect->Execute("select * from sondage WHERE date_fin > DATE_SUB(now(), INTERVAL 3 MONTH) ORDER BY date_fin ASC");
$nbsondages=$sondage->RecordCount();

echo $nbsondages.' '. _("polls in the database at this time") .'<br><br>'."\n";

// tableau qui affiche tous les sondages de la base
echo '<table border=1>'."\n";
echo '<tr align=center><td>'. _("Poll ID") .'</td><td>'. _("Format") .'</td><td>'. _("Title") .'</td><td>'. _("Author") .'</td><td>'. _("Expiration's date") .'</td><td>'. _("Users") .'</td><td colspan=3>'. _("Actions") .'</td>'."\n";

$i = 0;
while($dsondage = $sondage->FetchNextObject(false)) {
  /* possible en 1 bonne requête dans $sondage */
  $sujets=$connect->Execute( "select * from sujet_studs where id_sondage='$dsondage->id_sondage'");
  $dsujets=$sujets->FetchObject(false);

  $user_studs=$connect->Execute( "select * from user_studs where id_sondage='$dsondage->id_sondage'");
  $nbuser=$user_studs->RecordCount();

  echo '<tr align=center><td>'.$dsondage->id_sondage.'</td><td>'.$dsondage->format.'</td><td>'. stripslashes($dsondage->titre).'</td><td>'.stripslashes($dsondage->nom_admin).'</td>';

  if (strtotime($dsondage->date_fin) > time()) {
    echo '<td>'.date("d/m/y",strtotime($dsondage->date_fin)).'</td>';
  } else {
    echo '<td><font color=#FF0000>'.date("d/m/y",strtotime($dsondage->date_fin)).'</font></td>';
  }
  
  echo'<td>'.$nbuser.'</td>'."\n";
  echo '<td><a href="'.getUrlSondage($dsondage->id_sondage).'">'. _("See the poll") .'</a></td>'."\n";
  echo '<td><a href="'.getUrlSondage($dsondage->id_sondage_admin, true).'">'. _("Change the poll") .'</a></td>'."\n";
  echo '<td><input type="submit" name="supprimersondage'.$dsondage->id_sondage.'" value="'. _("Remove the poll") .'"></td>'."\n";

  echo '</tr>'."\n";
  $i++;
}

echo '</table>'."\n";  
// fin du formulaire et de la page web
echo '</form>'."\n";
echo'</div>'."\n";
echo '<div class="separateur">&nbsp;</div>';
echo '</body>'."\n";
echo '</html>'."\n";

// si on annule la suppression, rafraichissement de la page
if (issetAndNoEmpty('annulesuppression') === true) {
}
