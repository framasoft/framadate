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

//si les variables de session ne snot pas valides, il y a une erreur
if (!issetAndNoEmpty('nom', $_SESSION) && !issetAndNoEmpty('adresse', $_SESSION) && !issetAndNoEmpty('commentaires', $_SESSION) && !issetAndNoEmpty('mail', $_SESSION)) {
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
  print _("Back to the homepage of ") . ' ' . '<a href="index.php">' . NOMAPPLICATION . '</a>.' . "\n";
  echo '<br><br><br>'."\n";
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
              $choixdate .= $_SESSION["horaires$i"][$j];
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
  echo '<html>'."\n";
  echo '<head>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<title>'.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="style.css">'."\n";
  echo '<script type="text/javascript" src="block_enter.js"></script>';
  echo '</head>'."\n";
  echo '<body>'."\n";
  
  //Debut du formulaire et bandeaux de tete
  echo '<form name="formulaire" action="choix_date.php" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
  logo();
  bandeau_tete();
  bandeau_titre(_("Poll dates (2 on 2)"));
  sous_bandeau_choix();
  
  //affichage de l'aide pour les jours
  echo '<div class=bodydate>'."\n";
  echo _("Select your dates amoung the free days (green). The selected days are in blue.<br> You can unselect a day previously selected by clicking again on it.") ."\n";
  echo '</div>'."\n";
  
  //debut du tableau qui affiche le calendrier
  echo '<div class=calendrier>'."\n";
  echo '<table align=center>'."\n";
  echo '<tr><td><input type="image" name="anneeavant" value="<<" src="images/rewind.png"></td><td><input type="image" name="moisavant" value="<" src="images/previous.png"></td><td width="150px" align="center"> '.$motmois.' '.$_SESSION["annee"].' </td><td><input type="image" name="moisapres" value=">" src="images/next.png"></td><td><input type="image" name="anneeapres" value=">>" src="images/fforward.png"></td><td></td><td></td><td></td><td></td><td></td><td><input type="image" name="retourmois" value="Aujourd\'hui" src="images/reload.png"></td></tr>'."\n";
  echo '</table>'."\n";
  echo '<table>'."\n";
  echo '<tr>'."\n";
  
  //affichage des jours de la semaine en haut du tableau
  for($i = 0; $i < 7; $i++) {
    echo '<td class="joursemaine">'. strftime('%A',mktime(0,0,0,0, $i,10)) .'</td>';
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
  if (issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x')) {
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
            $errheure[$i][$j]=true;
            $erreur=true;
            $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
          }
        }
      }
    }
  }
  
  echo '<div class=bodydate>'."\n";
  
  //affichage de tous les jours choisis
  if (issetAndNoEmpty('totalchoixjour', $_SESSION) && (!issetAndNoEmpty('choixheures_x') || $erreur)) {
    //affichage des jours
    echo '<br>'."\n";
    echo '<H2>'. _("Selected days") .' :</H2>'."\n";
    //affichage de l'aide pour les jours
    echo _("For each selected day, you can choose, or not, meeting hours in the following format :<br>- empty,<br>- \"8h\", \"8H\" or \"8:00\" to give a meeting's start hour,<br>- \"8-11\", \"8h-11h\", \"8H-11H\" ou \"8:00-11:00\" to give a meeting's start and end hour,<br>- \"8h15-11h15\", \"8H15-11H15\" ou \"8:15-11:15\" for the same thing but with minutes.") .'<br><br>'."\n";
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<td></td>'."\n";
    
    for ($i = 0; $i < $_SESSION["nbrecaseshoraires"]; $i++) {
      $j = $i+1;
      echo '<td classe=somme>'. _("Time") .' '.$j.'</center></td>'."\n";
    }
    
    if ($_SESSION["nbrecaseshoraires"] < 10) {
      echo '<td classe=somme><input type="image" name="ajoutcases" src="images/add-16.png"></td>'."\n";
    }
    
    echo '</tr>'."\n";
    
    //affichage de la liste des jours choisis
    for ($i=0;$i<count($_SESSION["totalchoixjour"]);$i++) {
      echo '<tr>'."\n";
      if ($_SESSION["langue"]=="EN") {
        echo '<td>'.date("l, F jS Y", $_SESSION["totalchoixjour"][$i]).' : </td>'."\n";
      } else {
        echo '<td>'.strftime(_("%A, den %e. %B %Y"), $_SESSION["totalchoixjour"][$i]).' : </td>'."\n";
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
        echo '<td><b><font color=#FF0000>'. _("Bad format!") .'</font></b></td>'."\n";
      }
      
      echo '</tr>'."\n";
    }
    
    echo '</table>'."\n";
    
    //affichage des boutons de formulaire pour annuler, effacer les jours ou créer le sondage
    echo '<table>'."\n";
    echo '<tr>'."\n";
    echo '<td><input type=submit name="reset" value="'. _("Remove all days") .'"></td><td><input type=submit name="reporterhoraires" value="'. _("Copy hours of the first day") .'"></td><td><input type=submit name="resethoraires" value="'. _("Remove all hours") .'"></td></tr>'."\n";
    echo'<tr><td><br></td></tr>'."\n";
    echo '<tr><td>'. _("Next") .'</td><td><input type=image name="choixheures" value="'. _("Next") .'" src="images/next-32.png"></td></tr>'."\n";
    echo '</table>'."\n";
    
    //si un seul jour et aucunes horaires choisies, : message d'erreur
    if ((issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x')) && (count($_SESSION["totalchoixjour"])=="1" && $_POST["horaires0"][0]=="" && $_POST["horaires0"][1]=="" && $_POST["horaires0"][2]=="" && $_POST["horaires0"][3]=="" && $_POST["horaires0"][4]=="")) {
      echo '<table><tr><td colspan=3><font color=#FF0000>'. _("Enter more choices for the voters") .'</font><br></td></tr></table>'."\n";
      $erreur=true;
    }
  }

  //s'il n'y a pas d'erreur et que le bouton de creation est activé, on demande confirmation
  if (!$erreur  && (issetAndNoEmpty('choixheures') || issetAndNoEmpty('choixheures_x'))) {
    $taille_tableau=sizeof($_SESSION["totalchoixjour"])-1;
    $jour_arret = $_SESSION["totalchoixjour"][$taille_tableau]+200000;
    if ($_SESSION["langue"]=="EN") {
      $date_fin=date("l, F jS Y", $jour_arret);
    } else {
      $date_fin=strftime(_("%A, den %e. %B %Y"), $jour_arret);
    }
    
    echo '<br><div class="presentationdatefin">'. _("Your poll will expire automatically 2 days after the last date of your poll.") .'<br></td></tr><tr><td><br>'. _("Removal date") .' : <b> '.$date_fin.'</b><br><br>'."\n";
    echo '</div>'."\n";
    echo '<div class="presentationdatefin">'."\n";
    echo '<font color="#FF0000">'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll. <br><br>Then, you will receive quickly an email contening the link to your poll for sending it to the voters.") .'</font>'."\n";
    echo'</div>'."\n";
    // echo'<p class=affichageexport>'."\n";
    // echo 'Pour finir la cr&eacute;ation du sondage, cliquez sur le bouton <img src="images/add-16.png" alt="ajout"> ci-dessous'."\n";
    // echo '</p>'."\n";
    echo '<table>'."\n";
    echo '<tr><td>'. _("Back to hours") .'</td><td></td><td><input type="image" name="retourhoraires" src="images/back-32.png"></td></tr>'."\n";
    echo'<tr><td>'. _("Create the poll") .'</td><td></td><td><input type="image" name="confirmation" value="Valider la cr&eacute;ation" src="images/add.png"></td></tr>'."\n";
    echo '</table>'."\n";
  }
  
  echo '</tr>'."\n";
  echo '</table>'."\n";
  echo '<a name=bas></a>'."\n";
  //fin du formulaire et bandeau de pied
  echo '</form>'."\n";
  //bandeau de pied
  echo '<br><br><br><br>'."\n";
  echo '</div>'."\n";
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