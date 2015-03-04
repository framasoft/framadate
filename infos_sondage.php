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

function fromPostOrEmpty($postKey) {
    return isset($_POST[$postKey]) ? Utils::htmlEscape($_POST[$postKey]) : '';
}

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

// We clean the data
$poursuivre = filter_input(INPUT_POST, 'poursuivre', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(creation_sondage_date|creation_sondage_autre)$/']]);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
$mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
$editable = filter_input(INPUT_POST, 'editable', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);
$receiveNewVotes = filter_input(INPUT_POST, 'receiveNewVotes', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);
$receiveNewComments = filter_input(INPUT_POST, 'receiveNewComments', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);


// On initialise également les autres variables
$error_on_mail = false;
$error_on_title = false;
$error_on_name = false;
$error_on_description = false;

#tests
if (!empty($_POST['poursuivre'])) {
    $_SESSION['form']->title = $title;
    $_SESSION['form']->admin_name = $name;
    $_SESSION['form']->admin_mail = $mail;
    $_SESSION['form']->description = $description;
    $_SESSION['form']->editable = ($editable !== null) ? true : false;
    $_SESSION['form']->receiveNewVotes = ($receiveNewVotes !== null) ? true : false;
    $_SESSION['form']->receiveNewComments = ($receiveNewComments !== null) ? true : false;

    if ($config['use_smtp']==true) {
        if (empty($mail)) {
            $error_on_mail = true;
        }
    }

    if ($title !== $_POST['title']) {
        $error_on_title = true;
    }

    if ($name !== $_POST['name']) {
        $error_on_name = true;
    }

    if ($description !== $_POST['description']) {
        $error_on_description = true;
    }

    // Si pas d'erreur dans l'adresse alors on change de page vers date ou autre
    if ($config['use_smtp'] == true) {
        $email_OK = $mail && !$error_on_mail;
    } else {
        $email_OK = true;
    }

    if ($title && $name && $email_OK && ! $error_on_title && ! $error_on_description && ! $error_on_name) {

        if ( $poursuivre == 'creation_sondage_date' ) {
            header('Location:choix_date.php');
            exit();
        }

        if ( $poursuivre == 'creation_sondage_autre' ) {
            header('Location:choix_autre.php');
            exit();
        }

    } else {
        // Title Erreur !
        Utils::print_header( _('Error!').' - '._('Poll creation (1 on 3)') );
    }
} else {
    // Title OK (formulaire pas encore rempli)
    Utils::print_header( _('Poll creation (1 on 3)') );
}

bandeau_titre( _('Poll creation (1 on 3)') );

/*
 * Préparation des messages d'erreur
 */

$errors = array (
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

if (!empty($_POST['poursuivre'])) {
    if (empty($_POST['title'])) {
        $errors['title']['aria'] = 'aria-describeby="poll_title_error" ';
        $errors['title']['class'] = ' has-error';
        $errors['title']['msg'] = '<div class="alert alert-danger" ><p id="poll_title_error">' . _('Enter a title') . '</p></div>';
    } elseif ($error_on_title) {
        $errors['title']['aria'] = 'aria-describeby="poll_title_error" ';
        $errors['title']['class'] = ' has-error';
        $errors['title']['msg'] = '<div class="alert alert-danger"><p id="poll_title_error">' . _('Something is wrong with the format') . '</p></div>';
    }

    if ($error_on_description) {
        $errors['description']['aria'] = 'aria-describeby="poll_comment_error" ';
        $errors['description']['class'] = ' has-error';
        $errors['description']['msg'] = '<div class="alert alert-danger"><p id="poll_comment_error">' . _('Something is wrong with the format') . '</p></div>';
    }

    if (empty($_POST['name'])) {
        $errors['name']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['name']['class'] = ' has-error';
        $errors['name']['msg'] = '<div class="alert alert-danger"><p id="poll_name_error">' . _('Enter a name') . '</p></div>';
    } elseif ($error_on_name) {
        $errors['name']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['name']['class'] = ' has-error';
        $errors['name']['msg'] = '<div class="alert alert-danger"><p id="poll_name_error">' . _('Something is wrong with the format') . '</p></div>';
    }

    if (empty($_POST['mail'])) {
        $errors['email']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['email']['class'] = ' has-error';
        $errors['email']['msg'] = '<div class="alert alert-danger"><p id="poll_email_error">' . _('Enter an email address') . '</p></div>';
    } elseif ($error_on_mail) {
        $errors['email']['aria'] = 'aria-describeby="poll_email_error" ';
        $errors['email']['class'] = ' has-error';
        $errors['email']['msg'] = '<div class="alert alert-danger"><p id="poll_email_error">' . _('The address is not correct! You should enter a valid email address (like r.stallman@outlock.com) in order to receive the link to your poll.') . '</p></div>';
    }
}
/*
 *  Préparation en fonction des paramètres de session
 */

// REMOTE_USER ?
/**
 * @return string
 */

if (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) {
    $input_name = '<input type="hidden" name="name" value="'.Utils::htmlEscape($_POST['name']).'" />'.$_SESSION['form']->admin_name;
    $input_email = '<input type="hidden" name="mail" value="'.Utils::htmlEscape($_POST['mail']).'">'.$_SESSION['form']->admin_mail;
} else {
    $input_name = '<input id="yourname" type="text" name="name" class="form-control" '.$errors['name']['aria'].' value="'. fromPostOrEmpty('name') .'" />';
    $input_email = '<input id="email" type="text" name="mail" class="form-control" '.$errors['email']['aria'].' value="'. fromPostOrEmpty('mail') .'" />';
}

// Checkbox checked ?
if ($_SESSION['form']->editable) {
    $editable = 'checked';
}

if ($_SESSION['form']->receiveNewVotes) {
    $receiveNewVotes = 'checked';
}

if ($_SESSION['form']->receiveNewComments) {
    $receiveNewComments = 'checked';
}

// Display form
echo '
<div class="row" style="display:none" id="form-block">
    <div class="col-md-8 col-md-offset-2" >
    <form name="formulaire" id="formulaire" action="' . Utils::get_server_name() . 'infos_sondage.php" method="POST" class="form-horizontal" role="form">

        <div class="alert alert-info">
            <p>'. _('You are in the poll creation section.').' <br /> '._('Required fields cannot be left blank.') .'</p>
        </div>

        <div class="form-group'.$errors['title']['class'].'">
            <label for="poll_title" class="col-sm-4 control-label">' . _('Poll title') . ' *</label>
            <div class="col-sm-8">
                <input id="poll_title" type="text" name="title" class="form-control" '.$errors['title']['aria'].' value="'. fromPostOrEmpty('title') .'" />
            </div>
        </div>
            '.$errors['title']['msg'].'
        <div class="form-group'.$errors['description']['class'].'">
            <label for="poll_comments" class="col-sm-4 control-label">'. _('Description') .'</label>
            <div class="col-sm-8">
                <textarea id="poll_comments" name="description" class="form-control" '.$errors['description']['aria'].' rows="5">'. fromPostOrEmpty('description') .'</textarea>
            </div>
        </div>
            '.$errors['description']['msg'].'
        <div class="form-group'.$errors['name']['class'].'">
            <label for="yourname" class="col-sm-4 control-label">'. _('Your name') .' *</label>
            <div class="col-sm-8">
                '.$input_name.'
            </div>
        </div>
            '.$errors['name']['msg'];
if ($config['use_smtp']==true) {
    echo '
        <div class="form-group'.$errors['email']['class'].'">
            <label for="email" class="col-sm-4 control-label">'. _('Your email address') .' *<br /><span class="small">'. _('(in the format name@mail.com)') .'</span></label>
            <div class="col-sm-8">
                '.$input_email.'
            </div>
        </div>
        '.$errors['email']['msg'];
}
echo '
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
              <div class="checkbox">
                <label>
                    <input type=checkbox name="editable" '.$editable.' id="editable">'. _('Voters can modify their vote themselves.') .'
                </label>
              </div>
            </div>
        </div>';
if ($config['use_smtp']==true) {
    echo '<div class="form-group">
        <div class="col-sm-offset-4 col-sm-8">
          <div class="checkbox">
            <label>
                <input type=checkbox name="receiveNewVotes" '.$receiveNewVotes.' id="receiveNewVotes">'. _('To receive an email for each new vote.') .'
            </label>
          </div>
        </div>
    </div>';
    echo '<div class="form-group">
        <div class="col-sm-offset-4 col-sm-8">
          <div class="checkbox">
            <label>
                <input type=checkbox name="receiveNewComments" '.$receiveNewComments.' id="receiveNewComments">'. _('To receive an email for each new comment.') .'
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

        <script type="text/javascript">document.formulaire.title.focus();</script>

    </form>
    </div>
</div>';

echo '
<script>
    document.getElementById("form-block").setAttribute("style", "");
</script>
<noscript>
    <div class="alert alert-danger">'.
        _('Javascript is disabled on your browser. Its activation is required to create a poll.')
    .'</div>
</noscript>
';


bandeau_pied();
