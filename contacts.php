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
    print_header( _("Make your polls") );
    bandeau_titre(_("Make your polls"));

    echo '
    <div class=corpscentre>
        <h2>' . _("Your message has been sent!") . '</h2>
        <p>' . _("Back to the homepage of ") . ' <a href="' . get_server_name() . '"> ' . NOMAPPLICATION . '</a>.</p>
    </div>'."\n";
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
    print_header( _("Contact us") );
    bandeau_titre(_("Contact us"));

    echo '
    <form name=formulaire action="'.get_server_name().'contacts.php" method="POST">
        <p>' . _("If you have questions, you can send a message here.") . '</p>
        <p><label for="nom">' . _("Your name") .' :</label><br />
        <input type="text" size="40" maxlength="64" id="nom" name="nom" value="'.$_SESSION["nom"].'"></p>';

    if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["nom"]=="") {
        echo '<p class="error">'. _("Enter a name") .'</p>'; // /!\ manque un aria-describeby
    }

    echo '
        <p><label for="adresse_mail">' . _("Your email address ") . ' :</label><br />
        <input type="text" size="40" maxlength="64" id="adresse_mail" name="adresse_mail" value="'.$_SESSION["adresse_mail"].'"></p>'."\n";
  
    if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && empty($_SESSION["adresse_mail"]) === false && validateEmail($_SESSION["adresse_mail"]) === false) {
        echo '<p class="error">'. _("The address is not correct!") .'</p>'; // /!\ manque un aria-describeby
    }

    echo '
        <p><label for="question">' . _("Question") . ' :</label><br />
        <textarea name="question" id="question" rows="7" cols="40">'.$_SESSION["question"].'</textarea>';

    if ((isset($_POST['envoiquestion']) || isset($_POST['envoiquestion_x'])) && $_SESSION["question"]=="") {
        echo '<p class="error">Il faut poser une question !</p>'; // /!\ manque un aria-describeby
    }

    echo '
        <p><button type="submit" name="envoiquestion" value="'._("Send your question").'" class="button green poursuivre"><strong>'._("Send your question").'</strong></button></p>
    </form>'."\n";
    
    //bandeau de pied
    bandeau_pied();
}
