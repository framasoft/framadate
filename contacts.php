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

include_once('variables.php');
include_once( 'i18n.php' );
if (file_exists('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

// action du bouton annuler
if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && isset($_POST['nom']) && !empty($_POST['nom']) && isset($_POST['question']) && !empty($_POST['question'])) {
  $message=str_replace("\\","",$_POST["question"]);
  
  //envoi des mails

  if (isset($_POST['adresse_mail']) && !empty($_POST['adresse_mail']) && validateEmail($_POST['adresse_mail'])) {
    $headers = 'Reply-To: '.$_POST['adresse_mail'];
  } else {
    $headers = '' ;
  }

  sendEmail( ADRESSEMAILADMIN, "" . _("[CONTACT] You have sent a question ") . "".NOMAPPLICATION, "" . _("You have a question from a user ") . " ".NOMAPPLICATION."\n\n" . _("User") . " : ".$_POST["nom"]."\n\n" . _("User's email address") . " : $_POST[adresse_mail]\n\n" . _("Message") . " :".$message,$headers );
  if (isset($_POST['adresse_mail']) && !empty($_POST['adresse_mail']) && validateEmail($_POST['adresse_mail'])) {
    sendEmail( "$_POST[adresse_mail]", "" . _("[COPY] Someone has sent a question ") . "".NOMAPPLICATION, "" . _("Here is a copy of your question") . " :\n\n".$message." \n\n" . _("We're going to answer your question shortly.") . "\n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
  }
  
  //affichage de la page de confirmation d'envoi
  echo '<!DOCTYPE html>'."\n";
  echo '<html lang="'.$lang.'">'."\n";
  echo '<head>'."\n";
  echo '<title>'._("Make your polls").' - '.NOMAPPLICATION.'</title>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
  echo '</head>'."\n";
  echo '<body>'."\n";
  logo();
  bandeau_tete();
  bandeau_titre(_("Make your polls"));
  echo '<div class=corpscentre>'."\n";
  print "<h2>" . _("Your message has been sent!") . "</h2>"."\n";
  print "" . _("Back to the homepage of ") . " <a href=\"".get_server_name()."\"> ".NOMAPPLICATION."</a>."."\n";
  echo '</div>'."\n";
  bandeau_pied();
  session_unset();
} else {
  $post_var = array('question', 'nom', 'adresse_mail', );
  foreach ($post_var as $var) {
    if (isset($_POST[$var]) && !empty($_POST[$var])) {
      $_SESSION[$var] = $_POST[$var];
    } else {
      $_SESSION[$var] = null;
    }
  }
  
  //affichage de la page
  echo '<!DOCTYPE html>'."\n";
  echo '<html lang="'.strtolower($_SESSION['langue']).'">'."\n";
  echo '<head>'."\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
  echo '<title>'._("Contact us").' - '.NOMAPPLICATION.'</title>'."\n";
  echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
  echo '</head>'."\n";
  echo '<body>'."\n";

framanav();
  
  //debut du formulaire
  echo '<form name=formulaire action="'.get_server_name().'contacts.php" method="POST">'."\n";
  
  //bandeaux de tete
  logo();
  bandeau_tete();
  bandeau_titre(_("Contact us"));
  sous_bandeau();

  echo '<div class=corps>'."\n";
  echo _("If you have questions, you can send a message here.") .'<br /><br />'."\n";

  echo _("Your name") .' :<br />'."\n";
  echo '<input type="text" size="40" maxlength="64" name="nom" value="'.$_SESSION["nom"].'">';

  if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["nom"]=="") {
    echo ' <p class="error">'. _("Enter a name") .'</p>';
  }

  echo '<br /><br />'."\n";
  echo _("Your email address ") .' :<br />'."\n";
  echo '<input type="text" size="40" maxlength="64" name="adresse_mail" value="'.$_SESSION["adresse_mail"].'">'."\n";
  
  if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && empty($_SESSION["adresse_mail"]) === false && validateEmail($_SESSION["adresse_mail"]) === false) {
    echo ' <p class="error">'. _("The address is not correct!") .'</p>';
  }

  echo _("Question") .' :<br />'."\n";
  echo '<textarea name="question" rows="7" cols="40">'.$_SESSION["question"].'</textarea>';

  if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["question"]=="") {
    echo ' <p class="error">&nbsp;Il faut poser une question !</p>';
  }

  echo '<button type="submit" name="envoiquestion" value="'._("Send your question").'" class="button green poursuivre" alt="'._("Send your question").'"><strong>'._("Send your question").'</strong></button>';
  echo '</div>'."\n";
  echo '</form>'."\n";
  //bandeau de pied
  bandeau_pied();
  echo '</body>'."\n";
  echo '</html>'."\n";
}
