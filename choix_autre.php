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
include_once('creation_sondage.php');

if (file_exists('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

//si les variables de session ne sont pas valides, il y a une erreur
if (issetAndNoEmpty('titre', $_SESSION) === false || issetAndNoEmpty('nom', $_SESSION) === false || issetAndNoEmpty('adresse', $_SESSION) === false) {
  echo '<!DOCTYPE html>'."\n";
  echo '<html lang="'.$lang.'">'."\n";
  echo '<head>'."\n";
  echo '<meta charset="utf-8">'."\n";
  echo '<title>'._("Error!").' - '.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" href="'.get_server_name().'/style.css">'."\n";
  echo '</head>'."\n";
  echo '<body>'."\n";
  framanav();
  logo();
  bandeau_tete();
  bandeau_titre(_("Error!"));
  echo '<div class="corpscentre">'."\n";
  print "<h2>" . _("You haven't filled the first section of the poll creation.") . " !</h2>"."\n";
  print "" . _("Back to the homepage of ") . " <a href=\"".get_server_name()."\"> ".NOMAPPLICATION."</a>."."\n";
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
  
  
  if( ($testremplissage != "ok" && (isset($_POST["fin_sondage_autre"]) || isset($_POST["fin_sondage_autre_x"]))) || ($testdate === false) || ($erreur_injection) ) {
	// S'il y a des erreurs
    print_header(false, _("Error!") .' - '. _("Poll subjects (2 on 2)"), $lang);
  } else {
    print_header(false, _("Poll subjects (2 on 2)"), $lang);
  }
  
  echo '<body>'."\n";
  framanav();
  
  echo '<form name="formulaire" action="#bas" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
  logo();
  bandeau_tete();
  bandeau_titre(_("Poll subjects (2 on 2)"));
  sous_bandeau_choix();
  
  echo '<div class="corps">'."\n";
  echo '<p>'. _("Your poll aim is to make a choice between different subjects.") . '<br />' . _("Enter the subjects to vote for:") .'</p>'."\n";
  echo '<table>'."\n";
  
  //affichage des cases texte de formulaire
  for ($i = 0; $i < $_SESSION["nbrecases"]; $i++) {
    $j = $i + 1;
    if (isset($_SESSION["choix$i"]) === false) {
      $_SESSION["choix$i"] = '';
    }
    echo '<tr><td><label for="choix'.$i.'">'. _("Choice") .' '.$j.'</label> : </td><td><input type="text" name="choix[]" size="40" maxlength="40" value="'.str_replace("\\","",$_SESSION["choix$i"]).'" id="choix'.$i.'"></td></tr>'."\n";
  }
  
  echo '</table>'."\n";
  
  //focus javascript sur premiere case
  echo '<script type="text/javascript">'."\n";
  echo 'document.formulaire.choix0.focus();'."\n";
  echo '</script>'."\n";
  
  //ajout de cases supplementaires
  echo '<table><tr>'."\n";
  echo '<td>'. _("5 choices more") .'</td><td><input type="image" alt="'. _("5 choices more").'" name="ajoutcases" value="Retour" src="'.get_server_name().'images/add-16.png"></td>'."\n";
  echo '</tr></table>'."\n";
  
  //echo '<table><tr>'."\n";
  //echo '<td>'. _("Next") .'</td><td><input type="image" name="fin_sondage_autre" value="Cr&eacute;er le sondage" src="images/next-32.png"></td>'."\n";
  //echo '</tr></table>'."\n";

if (!isset($_POST["fin_sondage_autre_x"])) {
  echo '<button name="fin_sondage_autre_x" value="'._('Next').'" type="submit" class="button green poursuivre"><strong>'. _('Next') . '</strong> </button>';
  echo '<div style="clear:both"></div>';
}

  //test de remplissage des cases
  $testremplissage = '';
  for ($i=0;$i<$_SESSION["nbrecases"];$i++) {
    if (isset($_POST["choix"]) && issetAndNoEmpty($i, $_POST["choix"])) {
      $testremplissage="ok";
    }
  }
  
  //message d'erreur si aucun champ renseigné
  if ($testremplissage != "ok" && (isset($_POST["fin_sondage_autre"]) || isset($_POST["fin_sondage_autre_x"]))) {
    print "<p class=\"error\">" . _("Enter at least one choice") . "</p>"."\n";
    $erreur = true;
  }
  
  //message d'erreur si mauvaise date
  if ($testdate === false) {
    print "<p class=\"error\">" . _("Date must be have the format DD/MM/YYYY") . "</p>"."\n";
  }
  
  if ($erreur_injection) {
    print "<p class=\"error\">" . _("Characters \" < and > are not permitted") . "</p>\n";
  }
  
  if ((isset($_POST["fin_sondage_autre"]) || isset($_POST["fin_sondage_autre_x"])) && !$erreur && !$erreur_injection) {
    //demande de la date de fin du sondage
    echo '<div class=presentationdatefin>'."\n";
    echo '<p>'. _("Your poll will be automatically removed after 6 months."). '<br />' . _("You can fix another removal date for it.") .'</p>'."\n";
    echo '<label for="champdatefin">'. _("Removal date (optional)") .'</label> : <input type="text" class="champdatefin" id="champdatefin" aria-describedby="dateformat" name="champdatefin" value="'.$date_selected.'" size="10" maxlength="10"> <span id="dateformat">'. _("(DD/MM/YYYY)") .'</span>'."\n";
    echo '</div>'."\n";
    echo '<div class=presentationdatefin>'."\n";
    echo '<p class="error">'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll.").'<br /><br />'. _("Then, you will receive quickly an email contening the link to your poll for sending it to the voters.").'</p>'."\n";
    echo '</div>'."\n";
    //echo '<table>'."\n";
    //echo '<tr><td>'. _("Create the poll") .'</td><td><input type="image" name="confirmecreation" value="Valider la cr&eacute;ation"i src="images/add.png"></td></tr>'."\n";
    //echo '</table>'."\n";
    
    echo '<button name="confirmecreation" value="confirmecreation" type="submit" class="button green poursuivre margin-top"><strong>'. _('Make a poll') . '</strong> </button>';
    echo '<div style="clear:both"></div>';
    
  }
  
  //fin du formulaire et bandeau de pied
  echo '</form>'."\n";
  echo '<a id=bas></a>'."\n";
  echo '</div>'."\n";
  //bandeau de pied
  bandeau_pied_mobile();
  echo '</body>'."\n";
  echo '</html>'."\n";
}
