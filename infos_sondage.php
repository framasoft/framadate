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

if (!isset($_SESSION['form'])) {
    $_SESSION['form'] = new Form();
}

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Type de sondage : <button value="$_SESSION['form']->choix_sondage">
if ((isset($_GET['choix_sondage']) && $_GET['choix_sondage'] == 'date') ||
    (isset($_POST["choix_sondage"]) && $_POST["choix_sondage"] == 'creation_sondage_date')) {
    $choix_sondage = "creation_sondage_date";
    $_SESSION['form']->choix_sondage = $choix_sondage;
} else {
    $choix_sondage = "creation_sondage_autre";
    $_SESSION['form']->choix_sondage = $choix_sondage;
}

// On teste toutes les variables pour supprimer l'ensemble des warnings PHP
// On transforme en entites html les données afin éviter les failles XSS
$post_var = array('poursuivre', 'titre', 'nom', 'adresse', 'commentaires', 'editable', 'receiveNewVotes', 'creation_sondage_date', 'creation_sondage_autre');
foreach ($post_var as $var) {
    if (isset($_POST[$var]) === true) {
        $$var = htmlentities($_POST[$var], ENT_QUOTES, 'UTF-8');
    } else {
        $$var = null;
    }
}

// On initialise également les autres variables
$erreur_adresse = false;
$erreur_injection_titre = false;
$erreur_injection_nom = false;
$erreur_injection_commentaires = false;

#tests
if (!empty($_POST['poursuivre'])){
    $_SESSION['form']->title = $titre;
    $_SESSION['form']->admin_name = $nom;
    $_SESSION['form']->admin_mail = $adresse;
    $_SESSION['form']->description = $commentaires;
    $_SESSION['form']->editable = ($editable !== null) ? true : false;
    $_SESSION['form']->receiveNewVotes = ($receiveNewVotes !== null) ? true : false;

    if ($config['use_smtp']==true){
        if (Utils::isValidEmail($adresse) === false) {
            $erreur_adresse = true;
        }
    }

    if (preg_match(';<|>|";',$titre)) {
        $erreur_injection_titre = true;
    }

    if (preg_match(';<|>|";',$nom)) {
        $erreur_injection_nom = true;
    }

    if (preg_match(';<|>|";',$commentaires)) {
        $erreur_injection_commentaires = true;
    }

    // Si pas d'erreur dans l'adresse alors on change de page vers date ou autre
    if($config['use_smtp'] == true){
        $email_OK = $adresse && !$erreur_adresse;
    } else{
        $email_OK = true;
    }

    if ($titre && $nom && $email_OK && ! $erreur_injection_titre && ! $erreur_injection_commentaires && ! $erreur_injection_nom) {

        if ( $poursuivre == "creation_sondage_date" ) {
            header("Location:choix_date.php");
            exit();
        }

        if ( $poursuivre == "creation_sondage_autre" ) {
            header("Location:choix_autre.php");
            exit();
        }

    } else {
        // Title Erreur !
        Utils::print_header( _("Error!").' - '._("Poll creation (1 on 3)") );
    }
} else {
    // Title OK (formulaire pas encore rempli)
    Utils::print_header( _("Poll creation (1 on 3)") );
}

bandeau_titre( _("Poll creation (1 on 3)") );

/*
 * Préparation des messages d'erreur
 */

$errors = array(
    'title' => array (
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'description' => array (
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'name' => array (
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'email' => array (
        'msg' => '',
        'aria' => '',
        'class' => ''
    )
);

if (!$_SESSION['form']->title && !empty($_POST['poursuivre'])) {
    $errors['title']['aria'] = 'aria-describeby="poll_title_error" '; $errors['title']['class'] = ' has-error';
    $errors['title']['msg'] = '<div class="alert alert-danger" ><p id="poll_title_error">' . _("Enter a title") . '</p></div>';
} elseif ($erreur_injection_titre) {
    $errors['title']['aria'] = 'aria-describeby="poll_title_error" '; $errors['title']['class'] = ' has-error';
    $errors['title']['inject'] = '<div class="alert alert-danger"><p id="poll_title_error">' . _("Characters < > and \" are not permitted") . '</p></div>';
}

if ($erreur_injection_commentaires) {
    $errors['description']['aria'] = 'aria-describeby="poll_comment_error" '; $errors['description']['class'] = ' has-error';
    $errors['description']['msg'] = '<div class="alert alert-danger"><p id="poll_comment_error">' . _("Characters < > and \" are not permitted") . '</p></div>';
}

if (!$_SESSION['form']->admin_name && !empty($_POST['poursuivre'])) {
    $errors['name']['aria'] = 'aria-describeby="poll_name_error" '; $errors['name']['class'] = ' has-error';
    $errors['name']['msg'] = '<div class="alert alert-danger"><p id="poll_name_error">' . _("Enter a name") . '</p></div>';
} elseif ($erreur_injection_nom) {
    $errors['name']['aria'] = 'aria-describeby="poll_name_error" '; $errors['name']['class'] = ' has-error';
    $errors['name']['msg'] = '<div class="alert alert-danger"><p id="poll_name_error">' . _("Characters < > and \" are not permitted") . '</p></div>';
}

if (!$_SESSION['form']->admin_mail && !empty($_POST['poursuivre'])) {
    $errors['email']['aria'] = 'aria-describeby="poll_name_error" '; $errors['email']['class'] = ' has-error';
    $errors['email']['msg'] = '<div class="alert alert-danger"><p id="poll_email_error">' . _("Enter an email address") . '</p></div>';
} elseif ($erreur_adresse && !empty($_POST['poursuivre'])) {
    $errors['email']['aria'] = 'aria-describeby="poll_email_error" '; $errors['email']['class'] = ' has-error';
    $errors['email']['msg'] = '<div class="alert alert-danger"><p id="poll_email_error">' . _("The address is not correct! You should enter a valid email address (like r.stallman@outlock.com) in order to receive the link to your poll.") . '</p></div>';
}

/*
 *  Préparation en fonction des paramètres de session
 */

// REMOTE_USER ?
if (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) {
    $input_name = '<input type="hidden" name="nom" value="'.$_SESSION['form']->admin_name.'" />'.stripslashes($_SESSION['form']->admin_name);
} else {
    $input_name = '<input id="yourname" type="text" name="nom" class="form-control" '.$errors['name']['aria'].' value="'.stripslashes($_SESSION['form']->admin_name).'" />';
}

if (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) {
    $input_email = '<input type="hidden" name="adresse" value="'.$_SESSION['form']->admin_mail.'">'.$_SESSION['form']->admin_mail;
} else {
    $input_email = '<input id="email" type="text" name="adresse" class="form-control" '.$errors['email']['aria'].' value="'.$_SESSION['form']->admin_mail.'" />';
}

// Checkbox checked ?
if ($_SESSION['form']->editable) {
    $editable = 'checked';
}

if ($_SESSION['form']->receiveNewVotes) {
    $receiveNewVotes = 'checked';
}

// Display form
echo '
<div class="row">
    <div class="col-md-8 col-md-offset-2" >
    <form name="formulaire" id="formulaire" action="' . Utils::get_server_name() . 'infos_sondage.php" method="POST" class="form-horizontal" role="form">

        <div class="alert alert-info">
            <p>'. _("You are in the poll creation section.").' <br /> '._("Required fields cannot be left blank.") .'</p>
        </div>

        <div class="form-group'.$errors['title']['class'].'">
            <label for="poll_title" class="col-sm-4 control-label">' . _("Poll title") . ' *</label>
            <div class="col-sm-8">
                <input id="poll_title" type="text" name="titre" class="form-control" '.$errors['title']['aria'].' value="'.stripslashes($_SESSION['form']->title).'" />
            </div>
        </div>
            '.$errors['title']['msg'].'
        <div class="form-group'.$errors['description']['class'].'">
            <label for="poll_comments" class="col-sm-4 control-label">'. _("Description") .'</label>
            <div class="col-sm-8">
                <textarea id="poll_comments" name="commentaires" class="form-control" '.$errors['description']['aria'].' rows="5">'.stripslashes($_SESSION['form']->description).'</textarea>
            </div>
        </div>
            '.$errors['description']['msg'].'
        <div class="form-group'.$errors['name']['class'].'">
            <label for="yourname" class="col-sm-4 control-label">'. _("Your name") .' *</label>
            <div class="col-sm-8">
                '.$input_name.'
            </div>
        </div>
            '.$errors['name']['msg'];
if($config['use_smtp']==true){
    echo '
        <div class="form-group'.$errors['email']['class'].'">
            <label for="email" class="col-sm-4 control-label">'. _("Your email address") .' *<br /><span class="small">'. _("(in the format name@mail.com)") .'</span></label>
            <div class="col-sm-8">
                '.$input_email.'
            </div>
        </div>
        '.$errors['email']['msg'];
}
echo '
        <div class="form-group">
            <div class="col-sm-offset-1 col-sm-11">
              <div class="checkbox">
                <label>
                    <input type=checkbox name="editable" '.$editable.' id="editable">'. _("Voters can modify their vote themselves.") .'
                </label>
              </div>
            </div>
        </div>';
if($config['use_smtp']==true){
    echo '<div class="form-group">
        <div class="col-sm-offset-1 col-sm-11">
          <div class="checkbox">
            <label>
                <input type=checkbox name="receiveNewVotes" '.$receiveNewVotes.' id="receiveNewVotes">'. _("To receive an email for each new vote.") .'
            </label>
          </div>
        </div>
    </div>';
}
echo '
        <p class="text-right">
            <input type="hidden" name="choix_sondage" value="'. $choix_sondage .'"/>
            <button name="poursuivre" value="'. $choix_sondage .'" type="submit" class="btn btn-success" title="'. _('Go to step 2') . '">'. _('Next') . '</button>
        </p>

        <script type="text/javascript"> document.formulaire.titre.focus(); </script>

    </form>
    </div>
</div>';

bandeau_pied();
