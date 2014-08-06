<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate;

include_once __DIR__ . '/app/inc/init.php';

session_start();

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// action du bouton annuler
if ((isset($_POST['envoiquestion'])) &&
     isset($_POST['nom']) && !empty($_POST['nom']) &&
     isset($_POST['adresse_mail']) && !empty($_POST['adresse_mail']) && Utils::isValidEmail($_POST['adresse_mail']) &&
     isset($_POST['question']) && !empty($_POST['question'])) {
    $message=str_replace("\\","",$_POST["question"]);
    $headers = 'Reply-To: '.$_POST['adresse_mail'];

    Utils::sendEmail( ADRESSEMAILADMIN, "" . _("[CONTACT] You have sent a question ") . "".NOMAPPLICATION, "" . _("You have a question from a user ") . " ".NOMAPPLICATION."\n\n" . _("User") . " : ".$_POST["nom"]."\n\n" . _("User's email address") . " : $_POST[adresse_mail]\n\n" . _("Message") . " :".$message,$headers );
    if (isset($_POST['adresse_mail']) && !empty($_POST['adresse_mail']) && Utils::isValidEmail($_POST['adresse_mail'])) {
        Utils::sendEmail( "$_POST[adresse_mail]", "" . _("[COPY] Someone has sent a question ") . "".NOMAPPLICATION, "" . _("Here is a copy of your question") . " :\n\n".$message." \n\n" . _("We're going to answer your question shortly.") . "\n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
    }

    //affichage de la page de confirmation d'envoi
    Utils::print_header(_("Make your polls"));
    bandeau_titre(_("Make your polls"));

    echo '
    <div class="alert alert-success">
        <h2>' . _("Your message has been sent!") . '</h2>
        <p>' . _("Back to the homepage of ") . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a>.</p>
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

    /*
     * Préparation des messages d'erreur
     */

    $errors = array(
        'name' => array (
            'msg' => '',
            'aria' => '',
            'class' => ''
        ),
        'email' => array (
            'msg' => '',
            'aria' => '',
            'class' => ''
        ),
        'question' => array (
            'msg' => '',
            'aria' => '',
            'class' => ''
        ),
        'state' => false
    );

    if (isset($_POST['envoiquestion']) && $_SESSION["nom"]=="") {
        $errors['name']['aria'] = 'aria-describeby="#poll_name_error" '; $errors['name']['class'] = ' has-error';
        $errors['name']['msg'] = '<div class="alert alert-danger" ><p id="contact_name_error">'. _("Enter a name") .'</p></div>';
        $errors['state'] = true;
    }

    if (isset($_POST['envoiquestion']) && ($_SESSION["adresse_mail"] =="" || !Utils::isValidEmail($_SESSION["adresse_mail"]))) {
        $errors['email']['aria'] = 'aria-describeby="#poll_email_error" '; $errors['email']['class'] = ' has-error';
        $errors['email']['msg'] = '<div class="alert alert-danger" ><p id="contact_email_error">'. _("The address is not correct!") .'</p></div>';
        $errors['state'] = true;
    }
    if (isset($_POST['envoiquestion']) && $_SESSION["question"]=="") {
        $errors['question']['aria'] = 'aria-describeby="#poll_question_error" '; $errors['question']['class'] = ' has-error';
        $errors['question']['msg'] = '<div class="alert alert-danger" ><p id="contact_question_error">'. _("You must ask a question!") .'</p></div>';
        $errors['state'] = true;
    }

    //affichage de la page
    if($errors['state']) {
        Utils::print_header( _("Error!").' - '._("Contact us") );
    } else {
        Utils::print_header( _("Contact us") );
    }
        bandeau_titre(_("Contact us"));

    echo '
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form name=formulaire action="' . Utils::get_server_name() . 'contacts.php" method="POST" class="form-horizontal" role="form">
                <p>' . _("If you have questions, you can send a message here.") . '</p>
                <div class="form-group'.$errors['name']['class'].'">
                    <label for="name" class="col-sm-5 control-label">' . _("Your name") .'</label>
                    <div class="col-sm-7">
                        <input type="text" maxlength="64" id="name" name="nom" class="form-control" '.$errors['name']['aria'].' value="'.$_SESSION["nom"].'" />
                    </div>
                </div>
                    '.$errors['name']['msg'].'
                <div class="form-group'.$errors['email']['class'].'">
                    <label for="email" class="col-sm-5 control-label">' . _("Your email address ") . '</label>
                    <div class="col-sm-7">
                        <input type="text" maxlength="64" id="email" name="adresse_mail" class="form-control" '.$errors['email']['aria'].' value="'.$_SESSION["adresse_mail"].'" />
                    </div>
                </div>
                    '.$errors['email']['msg'].'
                <div class="form-group'.$errors['question']['class'].'">
                    <label for="question" class="col-sm-5 control-label">' . _("Question") . '</label>
                    <div class="col-sm-7">
                        <textarea name="question" id="question" rows="7" class="form-control" '.$errors['question']['aria'].'>'.$_SESSION["question"].'</textarea>
                    </div>
                </div>
                    '.$errors['question']['msg'].'
                <p class="text-right"><button type="submit" name="envoiquestion" value="'._("Send your question").'" class="btn btn-success">'._("Send your question").'</button></p>
            </form>
        </div>
    </div>'."\n";

    //bandeau de pied
    bandeau_pied();
}
