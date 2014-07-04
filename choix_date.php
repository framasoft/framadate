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

if (is_readable('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

//si les variables de session ne snot pas valides, il y a une erreur
if (!issetAndNoEmpty('nom', $_SESSION) && !issetAndNoEmpty('adresse', $_SESSION) && !issetAndNoEmpty('commentaires', $_SESSION) && !issetAndNoEmpty('mail', $_SESSION)) {
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
  echo '<html lang="'.$lang.'">'."\n";
  echo '<head>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<title>'._("Error!").' - '.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
  echo '</head>'."\n";
  echo '<body>'."\n";
  framanav();
  logo();
  bandeau_tete();
  bandeau_titre(_("Error!"));
  echo '<div class=corpscentre corps>'."\n";
  print "<h2>" . _("You haven't filled the first section of the poll creation.") . " !</h2>"."\n";
  print _("Back to the homepage of ") . ' ' . '<a href="'.get_server_name().'">' . NOMAPPLICATION . '</a>.' . "\n";
  echo '</div>'."\n";
  //bandeau de pied
  //sur_bandeau_pied();
  bandeau_pied();
  echo '</body>'."\n";
  echo '</html>'."\n";
} else { //sinon on peut afficher le calendrier normalement
  //partie creation du sondage dans la base SQL
  //On prépare les données pour les inserer dans la base
  if (issetAndNoEmpty('confirmation') || issetAndNoEmpty('confirmation_x')) {
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        if ($_SESSION["horaires$i"][0] == "" && $_SESSION["horaires$i"][1] == "" && $_SESSION["horaires$i"][2] == "" && $_SESSION["horaires$i"][3] == "" && $_SESSION["horaires$i"][4] == "") {
          $choixdate.=",";
          $choixdate .= $_SESSION["totalchoixjour"][$i];
        } else {
          for ($j=0;$j<$_SESSION["nbrecaseshoraires"];$j++) {
            if ($_SESSION["horaires$i"][$j]!="") {
              $choixdate.=",";
              $choixdate .= $_SESSION["totalchoixjour"][$i];
              $choixdate.="@";
              // On remplace la virgule et l'arobase pour ne pas avoir de problème par la suite
              $choixdate .= str_replace(array(',', '@'), array('&#44;', '&#64;'), $_SESSION["horaires$i"][$j]);
            }
          }
        }
      }
    }
    
    $_SESSION["toutchoix"]=substr("$choixdate",1);
    ajouter_sondage();
  }
  
  //nombre de cases par défaut
  if(!issetAndNoEmpty('nbrecaseshoraires', $_SESSION)) {
    $_SESSION["nbrecaseshoraires"]=5;
  } elseif ((issetAndNoEmpty('ajoutcases') || issetAndNoEmpty('ajoutcases_x')) && $_SESSION["nbrecaseshoraires"] == 5) {
    $_SESSION["nbrecaseshoraires"]=10;
  }
  
  //valeurs de la date du jour actuel
  $jourAJ=date("j");
  $moisAJ=date("n");
  $anneeAJ=date("Y");
  
  // Initialisation des jour, mois et année
  if (issetAndNoEmpty('jour', $_SESSION) === false) {
    $_SESSION['jour']= date('j');
  }
  if (issetAndNoEmpty('mois', $_SESSION) === false) {
    $_SESSION['mois']= date('n');
  }
  if (issetAndNoEmpty('annee', $_SESSION) === false) {
    $_SESSION['annee']= date('Y');
  }
  
  //mise a jour des valeurs de session si bouton retour a aujourd'hui
  if ((!issetAndNoEmpty('anneeavant_x') && !issetAndNoEmpty('anneeapres_x') && !issetAndNoEmpty('moisavant_x') && !issetAndNoEmpty('moisapres_x') && !issetAndNoEmpty('choixjourajout')) && !issetAndNoEmpty('choixjourretrait') || (issetAndNoEmpty('retourmois') || issetAndNoEmpty('retourmois_x'))){
    $_SESSION["jour"]=date("j");
    $_SESSION["mois"]=date("n");
    $_SESSION["annee"]=date("Y");
  }
  
  //mise a jour des valeurs de session si mois avant
  if (issetAndNoEmpty('moisavant') || issetAndNoEmpty('moisavant_x')) {
    if ($_SESSION["mois"] == 1) {
      $_SESSION["mois"]   = 12;
      $_SESSION["annee"]  = $_SESSION["annee"]-1;
    } else {
      $_SESSION["mois"] -= 1;
    }
    
    //On sauvegarde les heures deja entrées
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
        }
      }
    }
  }
  
  //mise a jour des valeurs de session si mois apres
  if (issetAndNoEmpty('moisapres') || issetAndNoEmpty('moisapres_x')) {
    if ($_SESSION["mois"] == 12) {
      $_SESSION["mois"] = 1;
      $_SESSION["annee"] += 1;
    } else {
      $_SESSION["mois"] += 1;
    }
    
    //On sauvegarde les heures deja entrées
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
        }
      }
    }
  }
  
  //mise a jour des valeurs de session si annee avant
  if (issetAndNoEmpty('anneeavant') || issetAndNoEmpty('anneeavant_x')) {
    $_SESSION["annee"] -= 1;
    
    //On sauvegarde les heures deja entrées
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
        }
      }
    }
  }
  
  //mise a jour des valeurs de session si annee apres
  if (issetAndNoEmpty('anneeapres') || issetAndNoEmpty('anneeapres_x')) {
    $_SESSION["annee"] += 1;
    
    //On sauvegarde les heures deja entrées
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0;$j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
        }
      }
    }
  }
  
  //valeurs du nombre de jour dans le mois et du premier jour du mois
  $nbrejourmois = date("t", mktime(0, 0, 0, $_SESSION["mois"], 1, $_SESSION["annee"]));
  $premierjourmois = date("N", mktime(0, 0, 0, $_SESSION["mois"], 1, $_SESSION["annee"])) - 1;
  
  //le format du sondage est DATE
  $_SESSION["formatsondage"] = "D".$_SESSION["studsplus"];
  
  //traduction de la valeur du mois
  if (is_integer($_SESSION["mois"]) && $_SESSION["mois"] > 0 && $_SESSION["mois"] < 13) {
    $motmois=strftime('%B', mktime(0, 0, 0, $_SESSION["mois"], 10));
  } else {
    $motmois=strftime('%B');
  }
  
  //debut de la page web
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
  echo '<html lang="'.$lang.'">'."\n";
  echo '<head>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<title>'._("Poll dates (2 on 2)").' - '.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
  echo '<script type="text/javascript" src="'.get_server_name().'block_enter.js"></script>';
  echo '</head>'."\n";
  echo '<body>'."\n";
  
  //Debut du formulaire et bandeaux de tete
  framanav();
  logo();
  bandeau_tete();
  bandeau_titre(_("Poll dates (2 on 2)"));
  sous_bandeau_choix();

  echo '<form name="formulaire" action="'.get_server_name().'choix_date.php" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";

  
  //ajout pyg pour la cohérence graphique
  echo ' <div class="corps">';
  
  //affichage de l'aide pour les jours
  echo '<div class="bodydate information">'."\n";
  echo '<p>'._("Select your dates amoung the free days (green). The selected days are in blue.").'<br />'. _("You can unselect a day previously selected by clicking again on it.") .'</p>'."\n";
  echo '</div>'."\n";
  
  //debut du tableau qui affiche le calendrier
  echo '<div class=calendrier>'."\n";
  echo '<table align="center" summary="'. _('Calendar') .'">'."\n";
  echo '<tr><td colspan="7" align="center" class="choix_date_mois" scope="colgroup">';
  echo '<input type="image" style="float:left" name="anneeavant" value="<<" alt="' . _('Previous year') . '" src="'.get_server_name().'images/rewind.png" />'."\n";
  echo '<input type="image" style="float:left" name="moisavant" value="<" alt="' . _('Previous month') . '" src="'.get_server_name().'images/previous.png" />'."\n";
  echo $motmois.' '.$_SESSION["annee"]."\n";
  echo '<input type="image" style="float:right" name="anneeapres" value=">>" alt="' . _('Next year') . '" src="'.get_server_name().'images/fforward.png"/>'."\n";
  echo '<input type="image" style="float:right" name="moisapres" value=">" alt="' . _('Next month') . '" src="'.get_server_name().'images/next.png" />'."\n";
  echo '<input type="image" style="float:right" name="retourmois" value="Aujourd\'hui" alt="' . _('Today') . '" src="'.get_server_name().'images/reload.png"/>'."\n";
  echo '</td></tr><tr>'."\n";
  
  //affichage des jours de la semaine en haut du tableau
  for($i = 0; $i < 7; $i++) {
    echo '<td class="joursemaine" scope="col">'. strftime('%A',mktime(0,0,0,0, $i,10)) .'</td>';
  }
  
  echo '</tr>'."\n";
  
  //ajout d'une entrée dans la variable de session qui contient toutes les dates
  if (issetAndNoEmpty('choixjourajout')) {
    if (!isset($_SESSION["totalchoixjour"])) {
      $_SESSION["totalchoixjour"]=array();
    }
    
    // Test pour éviter les doublons dans la variable qui contient toutes les dates
    $journeuf = true;
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true && issetAndNoEmpty('choixjourajout') === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        if ($_SESSION["totalchoixjour"][$i] == mktime(0, 0, 0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"])) {
          $journeuf=false;
        }
      }
    }
    
    // Si le test est passé, alors on insere la valeur dans la variable de session qui contient les dates
    if ($journeuf && issetAndNoEmpty('choixjourajout') === true) {
      array_push ($_SESSION["totalchoixjour"],mktime (0,0,0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"]));
      sort ($_SESSION["totalchoixjour"]);
      $cle=array_search (mktime (0,0,0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"]), $_SESSION["totalchoixjour"]);
      
      //On sauvegarde les heures deja entrées
      for ($i = 0; $i < $cle; $i++) {
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          if (issetAndNoEmpty('horaires'.$i) === true && issetAndNoEmpty($i, $_POST['horaires'.$i]) === true) {
            $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
          }
        }
      }
      
      for ($i = $cle; $i < count($_SESSION["totalchoixjour"]); $i++) {
        $k = $i + 1;
        if (issetAndNoEmpty('horaires'.$i) === true && issetAndNoEmpty($i, $_POST['horaires'.$i]) === true) {
          for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
            $_SESSION["horaires$k"][$j] = $_POST["horaires$i"][$j];
          }
        }
      }
      
      unset($_SESSION["horaires$cle"]);
    }
  }
  
  //retrait d'une entrée dans la variable de session qui contient toutes les dates
  if (issetAndNoEmpty('choixjourretrait')) {
    //On sauvegarde les heures deja entrées
    for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
      //affichage des 5 cases horaires
      for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
        $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
      }
    }
    
    for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
      if ($_SESSION["totalchoixjour"][$i] == mktime(0, 0, 0, $_SESSION["mois"], $_POST["choixjourretrait"][0], $_SESSION["annee"])) {
        for ($j = $i; $j < count($_SESSION["totalchoixjour"]); $j++) {
          $k = $j+1;
          $_SESSION["horaires$j"] = $_SESSION["horaires$k"];
        }
        
        array_splice($_SESSION["totalchoixjour"], $i,1);
      }
    }
  }
  
  //report des horaires dans toutes les cases
  if (issetAndNoEmpty('reporterhoraires')) {
    $_SESSION["horaires0"] = $_POST["horaires0"];
    for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
      $j = $i+1;
      $_SESSION["horaires$j"] = $_SESSION["horaires$i"];
    }
  }
  
  //report des horaires dans toutes les cases
  if (issetAndNoEmpty('resethoraires')) {
    for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
      unset ($_SESSION["horaires$i"]);
    }
  }
  
  // affichage du calendrier
  echo '<tr>'."\n";
  
  for ($i = 0; $i < $nbrejourmois + $premierjourmois; $i++) {
    $numerojour = $i-$premierjourmois+1;
    
    // On saute a la ligne tous les 7 jours
    if (($i%7) == 0 && $i != 0) {
      echo '</tr><tr>'."\n";
    }
    
    // On affiche les jours precedants en gris et incliquables
    if ($i < $premierjourmois) {
      echo '<td class=avant></td>'."\n";
    } else {
      if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
        for ($j = 0; $j < count($_SESSION["totalchoixjour"]); $j++) {
          //affichage des boutons ROUGES
          if (date("j", $_SESSION["totalchoixjour"][$j]) == $numerojour && date("n", $_SESSION["totalchoixjour"][$j]) == $_SESSION["mois"] && date("Y", $_SESSION["totalchoixjour"][$j]) == $_SESSION["annee"]) {
            echo '<td align=center class=choisi><input type=submit class="bouton OFF" name="choixjourretrait[]" value="'.$numerojour.'"></td>'."\n";
            $dejafait = $numerojour;
          }
        }
      }
      
      //Si pas de bouton ROUGE alors on affiche un bouton VERT ou GRIS avec le numéro du jour dessus
      if (isset($dejafait) === false || $dejafait != $numerojour){
        //bouton vert
        if (($numerojour >= $jourAJ && $_SESSION["mois"] == $moisAJ && $_SESSION["annee"] == $anneeAJ) || ($_SESSION["mois"] > $moisAJ && $_SESSION["annee"] == $anneeAJ) || $_SESSION["annee"] > $anneeAJ) {
          echo '<td align=center class=libre><input type=submit class="bouton ON" name="choixjourajout[]" value="'.$numerojour.'"></td>'."\n";
        } else { //bouton gris
          echo '<td class=avant>'.$numerojour.'</td>'."\n";
        }
      }
    }
  }
  
  //fin du tableau
  echo '</tr>'."\n";
  echo '</table>'."\n";
  echo '</div>'."\n";
  
  //traitement de l'entrée des heures dans les cases texte
  $errheure = $erreur = false;
  if (issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x') ) {
    //On sauvegarde les heures deja entrées
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true && issetAndNoEmpty('nbrecaseshoraires', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
        }
      }
    }
    
    //affichage des horaires
    if (issetAndNoEmpty('totalchoixjour', $_SESSION) === true && issetAndNoEmpty('nbrecaseshoraires', $_SESSION) === true) {
      for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
        //affichage des 5 cases horaires
        for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
          $case = $j + 1;
          
          if (isset($_POST['horaires'.$i]) === false || isset($_POST['horaires'.$i][$j]) === false) {
            $errheure[$i][$j]=true;
            $erreur=true;
            $_SESSION["horaires$i"][$j]=$_POST["horaires$i"][$j];
            continue;
          }
          
          //si c'est un creneau type 8:00-11:00
          if (preg_match("/(\d{1,2}:\d{2})-(\d{1,2}:\d{2})/", $_POST["horaires$i"][$j], $creneaux)) {
            //on recupere les deux parties du preg_match qu'on redécoupe autour des ":"
            $debutcreneau=explode(":", $creneaux[1]);
            $fincreneau=explode(":", $creneaux[2]);
            
            //comparaison des heures de fin et de debut
            //si correctes, on entre les données dans la variables de session
            if ($debutcreneau[0] < 24 && $fincreneau[0] < 24 && $debutcreneau[1] < 60 && $fincreneau[1] < 60 && ($debutcreneau[0] < $fincreneau[0] || ($debutcreneau[0] == $fincreneau[0] && $debutcreneau[1] < $fincreneau[1]))) {
              $_SESSION["horaires$i"][$j] = $creneaux[1].'-'.$creneaux[2];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif (preg_match(";^(\d{1,2}h\d{0,2})-(\d{1,2}h\d{0,2})$;i", $_POST["horaires$i"][$j], $creneaux)) { //si c'est un creneau type 8h00-11h00
            //on recupere les deux parties du preg_match qu'on redécoupe autour des "H"
            $debutcreneau=preg_split("/h/i", $creneaux[1]);
            $fincreneau=preg_split("/h/i", $creneaux[2]);
            
            //comparaison des heures de fin et de debut
            //si correctes, on entre les données dans la variables de session
            if ($debutcreneau[0] < 24 && $fincreneau[0] < 24 && $debutcreneau[1] < 60 && $fincreneau[1] < 60 && ($debutcreneau[0] < $fincreneau[0] || ($debutcreneau[0] == $fincreneau[0] && $debutcreneau[1] < $fincreneau[1]))) {
              $_SESSION["horaires$i"][$j] = $creneaux[1].'-'.$creneaux[2];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif (preg_match(";^(\d{1,2}):(\d{2})$;", $_POST["horaires$i"][$j], $heures)) { //si c'est une heure simple type 8:00
            //si valeures correctes, on entre les données dans la variables de session
            if ($heures[1] < 24 && $heures[2] < 60) {
              $_SESSION["horaires$i"][$j] = $heures[0];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif (preg_match(";^(\d{1,2})h(\d{0,2})$;i", $_POST["horaires$i"][$j], $heures)) { //si c'est une heure encore plus simple type 8h
            //si valeures correctes, on entre les données dans la variables de session
            if ($heures[1] < 24 && $heures[2] < 60) {
              $_SESSION["horaires$i"][$j] = $heures[0];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif (preg_match(";^(\d{1,2})-(\d{1,2})$;", $_POST["horaires$i"][$j], $heures)) { //si c'est un creneau simple type 8-11
            //si valeures correctes, on entre les données dans la variables de session
            if ($heures[1] < $heures[2] && $heures[1] < 24 && $heures[2] < 24) {
              $_SESSION["horaires$i"][$j] = $heures[0];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif (preg_match(";^(\d{1,2})h-(\d{1,2})h$;", $_POST["horaires$i"][$j], $heures)) { //si c'est un creneau H type 8h-11h
            //si valeures correctes, on entre les données dans la variables de session
            if ($heures[1] < $heures[2] && $heures[1] < 24 && $heures[2] < 24) {
              $_SESSION["horaires$i"][$j] = $heures[0];
            } else { //sinon message d'erreur et nettoyage de la case
              $errheure[$i][$j]=true;
              $erreur=true;
            }
          } elseif ($_POST["horaires$i"][$j]=="") { //Si la case est vide
            unset($_SESSION["horaires$i"][$j]);
          } else { //pour tout autre format, message d'erreur
            //$errheure[$i][$j]=true;
            //$erreur=true;
            $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
          }
        }
      }
    }
  }
  
  echo '<div class="bodydate">'."\n";
  
  //affichage de tous les jours choisis
  if (issetAndNoEmpty('totalchoixjour', $_SESSION) && (!issetAndNoEmpty('choixheures_x') || $erreur)) {
    //affichage des jours
    echo '<br />'."\n";
    echo '<h2>'. _("Selected days") .' :</h2>'."\n";
    //affichage de l'aide pour les jours
    echo '<div class="information"><p>'._("For each selected day, you can choose, or not, meeting hours (e.g.: \"8h\", \"8:30\", \"8h-10h\", \"evening\", etc.)") .'</p></div>'."\n";
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<td></td>'."\n";
    
    for ($i = 0; $i < $_SESSION["nbrecaseshoraires"]; $i++) {
      $j = $i+1;
      echo '<th class="somme" scope="col">'. _("Time") .' '.$j.'</th>'."\n";
    }
    
    if ($_SESSION["nbrecaseshoraires"] < 10) {
      echo '<th class="somme"><input type="image" name="ajoutcases" src="'.get_server_name().'images/add-16.png"></th>'."\n";
    }
    
    echo '</tr>'."\n";
    
    //affichage de la liste des jours choisis
    for ($i=0;$i<count($_SESSION["totalchoixjour"]);$i++) {
      echo '<tr>'."\n";
      if ($_SESSION["langue"]=="EN") {
        echo '<th scope="row">'.date("l, F jS Y", $_SESSION["totalchoixjour"][$i]).' : </th>'."\n";
      } else {
        echo '<th scope="row">'.strftime(_("%A, den %e. %B %Y"), $_SESSION["totalchoixjour"][$i]).' : </th>'."\n";
      }
      
      $affichageerreurfindeligne=false;
      
      //affichage des cases d'horaires
      for ($j=0;$j<$_SESSION["nbrecaseshoraires"];$j++) {
        //si on voit une erreur, le fond de la case est rouge
        if (isset($errheure[$i][$j]) && $errheure[$i][$j]) {
          echo '<td><input type=text size="10" maxlength="11" name=horaires'.$i.'[] value="'.$_SESSION["horaires$i"][$j].'" style="background-color:#FF6666;"></td>'."\n";
          $affichageerreurfindeligne=true;
        } else { //sinon la case est vide normalement
          if (issetAndNoEmpty('horaires'.$i, $_SESSION) === false || issetAndNoEmpty($j, $_SESSION['horaires'.$i]) === false) {
            if (issetAndNoEmpty('horaires'.$i, $_SESSION) === true) {
              $_SESSION["horaires$i"][$j] = '';
            } else {
              $_SESSION["horaires$i"] = array();
              $_SESSION["horaires$i"][$j] = '';
            }
          }
          
          echo '<td><input type=text size="10" maxlength="11" name=horaires'.$i.'[] value="'.$_SESSION["horaires$i"][$j].'"></td>'."\n";
        }
      }
      
      if ($affichageerreurfindeligne) {
        echo '<td><p class="error">'. _("Bad format!") .'</p></td>'."\n";
      }
      
      echo '</tr>'."\n";
    }
    
    echo '</table>'."\n";
    
    //affichage des boutons de formulaire pour annuler, effacer les jours ou créer le sondage
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<td><input type=submit name="reset" value="'. _("Remove all days") .'"></td><td><input type=submit name="reporterhoraires" value="'. _("Copy hours of the first day") .'"></td><td><input type=submit name="resethoraires" value="'. _("Remove all hours") .'"></td></tr>'."\n";
#    echo'<tr><td><br /></td></tr>'."\n";
#    echo '<tr><td>'. _("Next") .'</td><td><input type=image name="choixheures" value="'. _("Next") .'" src="images/next-32.png"></td></tr>'."\n";
    echo '</table>'."\n";
    
    //patch vraiment crado : on donne le nom "choixheures_x" au bouton pour éviter d'avoir à cleaner le code
    echo '<br /><button name="choixheures_x" value="'. _("Next") .'" type="submit" class="button green poursuivre"><strong>'. _('Next') . '</strong> </button>';
    echo '<div style="clear:both"></div>';
    
    
    //si un seul jour et aucunes horaires choisies, : message d'erreur
    if ((issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x')) && (count($_SESSION["totalchoixjour"])=="1" && $_POST["horaires0"][0]=="" && $_POST["horaires0"][1]=="" && $_POST["horaires0"][2]=="" && $_POST["horaires0"][3]=="" && $_POST["horaires0"][4]=="")) {
      echo '<table><tr><td colspan=3><p class="error">'. _("Enter more choices for the voters") .'</p></td></tr></table>'."\n";
      $erreur=true;
    }
  }

  //s'il n'y a pas d'erreur et que le bouton de creation est activé, on demande confirmation
  if (!$erreur  && (issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x'))) {
    $taille_tableau=sizeof($_SESSION["totalchoixjour"])-1;
    $jour_arret = $_SESSION["totalchoixjour"][$taille_tableau]+2592000;
    if ($_SESSION["langue"]=="EN") {
      $date_fin=date("l, F jS Y", $jour_arret);
    } else {
      $date_fin=strftime(_("%A, den %e. %B %Y"), $jour_arret);
    }
    
    echo '<br /><div class="presentationdatefin"><p>'. _("Your poll will expire automatically 2 days after the last date of your poll.") .'</p><p>'. _("Removal date") .' : <b> '.$date_fin.'</b></p>'."\n";
    echo '</div>'."\n";
    echo '<div class="presentationdatefin">'."\n";
    echo '<p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll."). '</p><p>' . _("Then, you will receive quickly an email contening the link to your poll for sending it to the voters.") .'</p>'."\n";
    echo'</div>'."\n";
    // echo'<p class=affichageexport>'."\n";
    // echo 'Pour finir la cr&eacute;ation du sondage, cliquez sur le bouton <img src="images/add-16.png" alt="ajout"> ci-dessous'."\n";
    // echo '</p>'."\n";

    //echo '<table>'."\n";
    //echo '<tr><td>'. _("Back to hours") .'</td><td></td><td><input type="image" name="retourhoraires" src="images/back-32.png"></td></tr>'."\n";
    //echo'<tr><td>'. _("Create the poll") .'</td><td></td><td><input type="image" name="confirmation" value="Valider la cr&eacute;ation" src="images/add.png"></td></tr>'."\n";
    //echo '</table>'."\n";
    
    echo '<br/><br/>';
    // patch crado : on attribue les noms de boutons avec _x pour faire croire qu'on a cliqué sur une image
    echo '<button name="retourhoraires_x" value="retourhoraires" type="submit" class="button red retour"><strong>'. _('Back to hours') . '</strong> </button>';
    echo '<button name="confirmation_x" value="confirmation" type="submit" class="button green poursuivre"><strong>'. _('Create the poll') . '</strong> </button>';
    echo '<div style="clear:both"></div>';
  }
  
/*  echo '</tr>'."\n";
  echo '</table>'."\n";*/
  echo '<a name="bas"></a>'."\n";
  //fin du formulaire et bandeau de pied
  echo '</form>'."\n";
  //bandeau de pied
  //echo '<br /><br /><br /><br />'."\n";
  echo '</div>'."\n";
  
  echo '</div>'; // ajout pyg pour cohérence graphique
  
  bandeau_pied_mobile();
  echo '</body>'."\n";
  echo '</html>'."\n";
  
  //bouton de nettoyage de tous les jours choisis
  if (issetAndNoEmpty('reset')) {
    for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
      for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
        unset($_SESSION["horaires$i"][$j]);
      }
    }
    
    unset($_SESSION["totalchoixjour"]);
    unset($_SESSION["nbrecaseshoraires"]);
    echo '<meta http-equiv="refresh" content="0">';
  }
}
