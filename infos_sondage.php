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
include_once('fonctions.php');
if (file_exists('bandeaux_local.php'))
	include_once('bandeaux_local.php');
else
	include_once('bandeaux.php');

#tests
if (($_POST["creation_sondage_date"]||$_POST["creation_sondage_autre"]||$_POST["creation_sondage_date_x"]||$_POST["creation_sondage_autre_x"])){
	$_SESSION["titre"]=$_POST["titre"];
	$_SESSION["nom"]=$_POST["nom"];
	$_SESSION["adresse"]=$_POST["adresse"];
	$_SESSION["commentaires"]=$_POST["commentaires"];
	
	unset($_SESSION["studsplus"]);
	if ($_POST["studsplus"])
	  $_SESSION["studsplus"] =  '+';
	
	unset($_SESSION["mailsonde"]);
	if ($_POST["mailsonde"])
	  $_SESSION["mailsonde"] = true;
	
	if(!filter_var($_POST["adresse"], FILTER_VALIDATE_EMAIL) || strpos($_POST["adresse"], '@') === false) {
	  $erreur_adresse = true;
	}
	if (preg_match(';<|>|";',$_POST["titre"])){
	  $erreur_injection_titre = true;
	}
	if (preg_match(';<|>|";',$_POST["nom"])){
	  $erreur_injection_nom = true;
	}
	if (preg_match(';<|>|";',$_POST["commentaires"])){
	  $erreur_injection_commentaires = true;
	}
	
	// Si pas d'erreur dans l'adresse alors on change de page vers date ou autre
	if ($_POST["titre"] && $_POST["nom"] && $_POST["adresse"] &&
	    ! $erreur_adresse && ! $erreur_injection_titre && ! $erreur_injection_commentaires && ! $erreur_injection_nom ){
	  if ($_POST["creation_sondage_date"]||$_POST["creation_sondage_date_x"]){
	    header("Location:choix_date.php");
	    exit();
	  }
	  
	  if ($_POST["creation_sondage_autre"]||$_POST["creation_sondage_autre_x"]){
	    header("Location:choix_autre.php");
	    exit();
	  }
	}

	//En cas d'erreur, recuperation des variables deja entrées
	if (empty($_POST["titre"]) || empty($_POST["adresse"]) || empty($_POST["nom"])) {
	  $_SESSION["titre"]=$_POST["titre"];
	  $_SESSION["nom"]=$_POST["nom"];
	  $_SESSION["adresse"]=$_POST["adresse"];
	  $_SESSION["commentaires"]=$_POST["commentaires"];
	}

}

//affichage de la page

print_header(true);
echo '<body>'."\n";

//affichage des bandeaux de tete
logo();
bandeau_tete();
echo '<div class="bandeautitre">'. _("Poll creation (1 on 2)") .'</div>'."\n";
sous_bandeau();

// premier sondage ? test l'existence des schémas SQL avant d'aller plus loin
if(! check_table_sondage() ) {
  echo '<p style="color:#FF0000; text-align:center">' .
    _("STUdS is not properly installed, please check the 'INSTALL' to setup the database before continuing") .
    "</p>"."\n";
  bandeau_pied();
  echo '</body>'."\n";
  echo '</html>'."\n";
  die();
}

//debut du formulaire
echo '<form name="formulaire" action="infos_sondage.php" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
 
echo '<div class=corps>'."\n";
echo '<br>'. _("You are in the poll creation section. <br> Required fields cannot be left blank") .'<br><br>'."\n";

//Affichage des différents champs textes a remplir
echo '<table>'."\n";

echo '<tr><td>'. _("Poll title *: ") .'</td><td><input type="text" name="titre" size="40" maxlength="80" value="'.$_SESSION["titre"].'"></td>'."\n";
if (!$_SESSION["titre"]&&($_POST["creation_sondage_date"]||$_POST["creation_sondage_autre"]||$_POST["creation_sondage_date_x"]||$_POST["creation_sondage_autre_x"])){
	print "<td><font color=\"#FF0000\">" . _("Enter a title") . "</font></td>"."\n";
}
elseif ($erreur_injection_titre){
		print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}
echo '</tr>'."\n";
echo '<tr><td>'. _("Comments: ") .'</td><td><textarea name="commentaires" rows="7" cols="40">'.$_SESSION["commentaires"].'</textarea></td>'."\n";
if ($erreur_injection_commentaires){
		print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}
echo '</tr>'."\n";
echo '<tr><td>'. _("Your name*: ") .'</td><td>';
if (isset($_SERVER['REMOTE_USER']))
	echo '<input type="hidden" name="nom" size="40" maxlength="40" value="'.$_SESSION["nom"].'">'.$_SESSION["nom"].'</td>'."\n";
else
	echo '<input type="text" name="nom" size="40" maxlength="40" value="'.$_SESSION["nom"].'"></td>'."\n";
if (!$_SESSION["nom"]&&($_POST["creation_sondage_date"]||$_POST["creation_sondage_autre"]||$_POST["creation_sondage_date_x"]||$_POST["creation_sondage_autre_x"])){
	print "<td><font color=\"#FF0000\">" . _("Enter a name") . "</font></td>"."\n";
}
elseif ($erreur_injection_nom){
		print "<td><font color=\"#FF0000\">" . _("Characters < > and \" are not permitted") . "</font></td><br>"."\n";
}
echo '</tr>'."\n";
echo '<tr><td>'. _("Your e-mail address *: ") .'</td><td>';
if (isset($_SERVER['REMOTE_USER']))
	echo '<input type="hidden" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'">'.$_SESSION["adresse"].'</td>'."\n";
else
	echo '<input type="text" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'"></td>'."\n";
if (!$_SESSION["adresse"]&&($_POST["creation_sondage_date"]||$_POST["creation_sondage_autre"]||$_POST["creation_sondage_date_x"]||$_POST["creation_sondage_autre_x"])){
	print "<td><font color=\"#FF0000\">" . _("Enter an email address") . " </font></td>"."\n";
}
elseif ($erreur_adresse&&($_POST["creation_sondage_date"]||$_POST["creation_sondage_autre"]||$_POST["creation_sondage_date_x"]||$_POST["creation_sondage_autre_x"])){
	print "<td><font color=\"#FF0000\">" . _("The address is not correct! (You should enter a valid email address in order to receive the link to your poll)") . "</font></td>"."\n";
}

echo '</tr>'."\n";

echo '</table>'."\n";

//focus javascript sur le premier champ
echo '<script type="text/javascript">'."\n";
echo 'document.formulaire.titre.focus();'."\n";
echo '</script>'."\n";

echo '<br>'. _("The fields marked with * are required!") .'<br><br>'."\n";

#affichage du cochage par défaut
if (!$_SESSION["studsplus"]&&!$_POST["creation_sondage_date"]&&!$_POST["creation_sondage_autre"]&&!$_POST["creation_sondage_date_x"]&&!$_POST["creation_sondage_autre_x"]){$_SESSION["studsplus"]="+";}

if ($_SESSION["studsplus"]=="+"){$cocheplus="checked";}
echo '<input type=checkbox name=studsplus '.$cocheplus.'>'. _(" Voters can modify their vote themselves.") .'<br>'."\n";
if ($_SESSION["mailsonde"]){$cochemail="checked";}
echo '<input type=checkbox name=mailsonde '.$cochemail.'>'. _(" To receive an email for each new vote.") .'<br>'."\n";

//affichage des boutons pour choisir sondage date ou autre
echo '<br><table >'."\n";
echo '<tr><td>'. _("Schedule an event") .'</td><td></td> '."\n";
echo '<td><input type="image" name="creation_sondage_date" value="Trouver une date" src="images/calendar-32.png"></td></tr>'."\n";
echo '<tr><td>'. _("Make a choice") .'</td><td></td> '."\n";
echo '<td><input type="image" name="creation_sondage_autre" value="'
	 . _('Make a poll') . '" src="images/chart-32.png"></td></tr>'."\n";
echo '</table>'."\n";
echo '<br><br><br>'."\n";
echo '</div>'."\n";
echo '</form>'."\n";
//bandeau de pied
bandeau_pied();
echo '</body>'."\n";
echo '</html>'."\n";
?>
