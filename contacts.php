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

include_once('variables.php');
include_once( 'i18n.php' );
if (file_exists('bandeaux_local.php'))
	include_once('bandeaux_local.php');
else
	include_once('bandeaux.php');

// action du bouton annuler
if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && isset($_POST['nom']) && !empty($_POST['nom']) && isset($_POST['question']) && !empty($_POST['question'])){


	$message=str_replace("\\","",$_POST["question"]);
	
	//envoi des mails
	$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
	mail (ADRESSEMAILADMIN, "" . _("[CONTACT] You have sent a question ") . "".NOMAPPLICATION, "" . _("You have a question from a user ") . " ".NOMAPPLICATION."\n\n" . _("User") . " : ".$_POST["nom"]."\n\n" . _("User's email address") . " : $_POST[adresse_mail]\n\n" . _("Message") . " :".$message,$headers);
	if (isset($_POST['adresse_mail']) && !empty($_POST['adresse_mail']) && validateEmail($_POST['adresse_mail'])){
		$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
		mail ("$_POST[adresse_mail]", "" . _("[COPY] Someone has sent a question ") . "".NOMAPPLICATION, "" . _("Here is a copy of your question") . " :\n\n".$message." \n\n" . _("We're going to answer your question shortly.") . "\n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION,$headers);
	}

	//affichage de la page de confirmation d'envoi
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
	echo '<html>'."\n";
	echo '<head>'."\n";
	echo '<title>'.NOMAPPLICATION.'</title>'."\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
	echo '<link rel="stylesheet" type="text/css" href="style.css">'."\n";
	echo '</head>'."\n";
	echo '<body>'."\n";
	logo();
	bandeau_tete();
	bandeau_titre(_("Make your polls"));
	
	echo '<div class=corpscentre>'."\n";
	print "<H2>" . _("Your message has been sent!") . "</H2><br><br>"."\n";
	print "" . _("Back to the homepage of ") . " <a href=\"index.php\"> ".NOMAPPLICATION."</A>."."\n";
	echo '<br><br><br>'."\n";
	echo '</div>'."\n";
	
	bandeau_pied();

	session_unset();

}

else {
	$post_var = array('question', 'nom', 'adresse_mail', );
	foreach ($post_var as $var) {
		if (isset($_POST[$var]) && !empty($_POST[$var])) {
			$_SESSION[$var] = $_POST[$var];
		} else {
			$_SESSION[$var] = null;
		}
	}

	//affichage de la page
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
	echo '<html>'."\n";
	echo '<head>'."\n";
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
	echo '<title>'.NOMAPPLICATION.'</title>'."\n";
	echo '<link rel="stylesheet" type="text/css" href="style.css">'."\n";
	echo '</head>'."\n";
	echo '<body>'."\n";

	//debut du formulaire
	echo '<form name=formulaire action="contacts.php" method="POST">'."\n";

	//bandeaux de tete
	logo();
	bandeau_tete();
	bandeau_titre(_("Contact us"));
	sous_bandeau();

	//blablabla
	echo '<div class=corps>'."\n";
	echo _("If you have questions, you can send a message here.") .'<br><br>'."\n";

	echo _("Your name") .' :<br>'."\n";
	echo '<input type="text" size="40" maxlength="64" name="nom" value="'.$_SESSION["nom"].'">';

	if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["nom"]==""){
		echo ' <font color="#FF0000">'. _("Enter a name") .'</font>';
	}

	echo '<br><br>'."\n";
	echo _("Your email address ") .' :<br>'."\n";
	echo '<input type="text" size="40" maxlength="64" name="adresse_mail" value="'.$_SESSION["adresse_mail"].'">'."\n";


	echo '<br><br>';

	echo _("Question") .' :<br>'."\n";
	echo '<textarea name="question" rows="7" cols="40">'.$_SESSION["question"].'</textarea>';

	if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["question"]==""){
		echo ' <font color="#FF0000">&nbsp;Il faut poser une question !</font>';
	}

	echo '<br><br><br>'."\n";
	echo '<table>'."\n";
	echo '<tr><td>'. _("Send your question") .'</td><td><input type="image" name="envoiquestion" value="Envoyer votre question" src="images/next-32.png"></td></tr>'."\n";
	echo '</table>'."\n";
	echo '<br><br><br>'."\n";
	echo '</div>'."\n";
	echo '</form>'."\n";

	//bandeau de pied
	bandeau_pied();

	echo '</body>'."\n";
	echo '</html>'."\n";

}

?>
