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
include_once('creation_sondage.php');

if (file_exists('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

//si les variables de session ne sont pas valides, il y a une erreur
if (issetAndNoEmpty('titre', $_SESSION) === false || issetAndNoEmpty('nom', $_SESSION) === false || issetAndNoEmpty('adresse', $_SESSION) === false) {
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
  echo '<html>'."\n";
  echo '<head>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<title>'.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="style.css">'."\n";
  echo '</head>'."\n";
  echo '<body>'."\n";
  logo();
  bandeau_tete();
  bandeau_titre(_("Error!"));
  echo '<div class=corpscentre>'."\n";
  print "<H2>" . _("You haven't filled the first section of the poll creation.") . " !</H2>"."\n";
  print "" . _("Back to the homepage of ") . " <a href=\"index.php\"> ".NOMAPPLICATION."</A>."."\n";
  echo '<br><br><br>'."\n";
  echo '</div>'."\n";
  //bandeau de pied
  bandeau_pied();
  echo '</body>'."\n";
  echo '</html>'."\n";
} else {
  //partie creation du sondage dans la base SQL
  //On prépare les données pour les inserer dans la base
  
  $erreur = false;
  $testdate = true;
  $date_selected = '';
  
  if (isset($_POST["confirmecreation"]) || isset($_POST["confirmecreation_x"])) {
    //recuperation des données de champs textes
    $toutchoix = '';
    for ($i = 0; $i < $_SESSION["nbrecases"] + 1; $i++) {
      if (isset($_POST["choix"]) && issetAndNoEmpty($i, $_POST["choix"])) {
        $toutchoix.=',';
        $toutchoix.=str_replace(",", " ", htmlentities(html_entity_decode($_POST["choix"][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
      }
    }
    
    $toutchoix=substr("$toutchoix",1);
    $_SESSION["toutchoix"]=$toutchoix;
    
    if (issetAndNoEmpty('champdatefin')) {
      $registredate=explode("/",$_POST["champdatefin"]);
      if (is_array($registredate) === false || count($registredate) !== 3) {
        $testdate = false;
        $date_selected = $_POST["champdatefin"];
      } else {
        $time = mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
        if ($time === false || date('d/m/Y', $time) !== $_POST["champdatefin"]) {
          $testdate = false;
          $date_selected = $_POST["champdatefin"];
        } else {
          if (mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]) > time() + 250000) {
            $_SESSION["champdatefin"]=mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
          }
        }
      }
    } else {
      $_SESSION["champdatefin"]=time()+15552000;
    }
    
    if ($testdate === true) {
      //format du sondage AUTRE
      $_SESSION["formatsondage"]="A".$_SESSION["studsplus"];
      
      ajouter_sondage();
    } else {
      $_POST["fin_sondage_autre"] = 'ok';
    }
  }
  
  // recuperation des sujets pour sondage AUTRE
  $erreur_injection = false;
  if (isset($_SESSION["nbrecases"])) {
    for ($i = 0; $i < $_SESSION["nbrecases"]; $i++) {
      if (isset($_POST["choix"]) && isset($_POST["choix"][$i])) {
        $_SESSION["choix$i"]=htmlentities(html_entity_decode($_POST["choix"][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
      }
    }
  } else { //nombre de cases par défaut
    $_SESSION["nbrecases"]=10;
  }
  
  if (isset($_POST["ajoutcases"]) || isset($_POST["ajoutcases_x"])) {
    $_SESSION["nbrecases"]=$_SESSION["nbrecases"]+5;
  }
  
  print_header();
  echo '<body>'."\n";
  
  echo '<form name="formulaire" action="#bas" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
  logo();
  bandeau_tete();
  bandeau_titre(_("Poll subjects (2 on 2)"));
  sous_bandeau_choix();
  
  echo '<div class=corps>'."\n";
  echo '<br>'. _("Your poll aim is to make a choice between different subjects.<br>Enter the subjects to vote for:") .'<br><br>'."\n";
  echo '<table>'."\n";
  
  //affichage des cases texte de formulaire
  for ($i = 0; $i < $_SESSION["nbrecases"]; $i++) {
    $j = $i + 1;
    if (isset($_SESSION["choix$i"]) === false) {
      $_SESSION["choix$i"] = '';
    }
    echo '<tr><td>'. _("Choice") .' '.$j.' : </td><td><input type="text" name="choix[]" size="40" maxlength="40" value="'.str_replace("\\","",$_SESSION["choix$i"]).'" id="choix'.$i.'"></td></tr>'."\n";
  }
  
  echo '</table>'."\n";
  
  //focus javascript sur premiere case
  echo '<script type="text/javascript">'."\n";
  echo 'document.formulaire.choix0.focus();'."\n";
  echo '</script>'."\n";
  
  //ajout de cases supplementaires
  echo '<table><tr>'."\n";
  echo '<td>'. _("5 choices more") .'</td><td><input type="image" name="ajoutcases" value="Retour" src="images/add-16.png"></td>'."\n";
  echo '</tr></table>'."\n";
  echo'<br>'."\n";
  
  echo '<table><tr>'."\n";
  echo '<td>'. _("Next") .'</td><td><input type="image" name="fin_sondage_autre" value="Cr&eacute;er le sondage" src="images/next-32.png"></td>'."\n";
  echo '</tr></table>'."\n";
  
  //test de remplissage des cases
  $testremplissage = '';
  for ($i=0;$i<$_SESSION["nbrecases"];$i++) {
    if (isset($_POST["choix"]) && issetAndNoEmpty($i, $_POST["choix"])) {
      $testremplissage="ok";
    }
  }
  
  //message d'erreur si aucun champ renseigné
  if ($testremplissage != "ok" && (isset($_POST["fin_sondage_autre"]) || isset($_POST["fin_sondage_autre_x"]))) {
    print "<br><font color=\"#FF0000\">" . _("Enter at least one choice") . "</font><br><br>"."\n";
    $erreur = true;
  }
  
  //message d'erreur si mauvaise date
  if ($testdate === false) {
    print "<br><font color=\"#FF0000\">" . _("Date must be have the format DD/MM/YYYY") . "</font><br><br>"."\n";
  }
  
  if ($erreur_injection) {
    print "<font color=#FF0000>" . _("Characters \" < and > are not permitted") . "</font><br><br>\n";
  }
  
  if ((isset($_POST["fin_sondage_autre"]) || isset($_POST["fin_sondage_autre_x"])) && !$erreur && !$erreur_injection) {
    //demande de la date de fin du sondage
    echo '<br>'."\n";
    echo '<div class=presentationdatefin>'."\n";
    echo '<br>'. _("Your poll will be automatically removed after 6 months.<br> You can fix another removal date for it.") .'<br><br>'."\n";
    echo _("Removal date (optional)") .' : <input type="text" name="champdatefin" value="'.$date_selected.'" size="10" maxlength="10"> '. _("(DD/MM/YYYY)") ."\n";
    echo '</div>'."\n";
    echo '<div class=presentationdatefin>'."\n";
    echo '<font color=#FF0000>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll. <br><br>Then, you will receive quickly an email contening the link to your poll for sending it to the voters.") .'</font>'."\n";
    echo '</div>'."\n";
    echo '<br>'."\n";
    echo '<table>'."\n";
    echo '<tr><td>'. _("Create the poll") .'</td><td><input type="image" name="confirmecreation" value="Valider la cr&eacute;ation"i src="images/add.png"></td></tr>'."\n";
    echo '</table>'."\n";
  }
  
  //fin du formulaire et bandeau de pied
  echo '</form>'."\n";
  echo '<a name=bas></a>'."\n";
  echo '<br><br><br>'."\n";
  echo '</div>'."\n";
  //bandeau de pied
  bandeau_pied_mobile();
  echo '</body>'."\n";
  echo '</html>'."\n";
}