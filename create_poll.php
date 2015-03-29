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

use Framadate\Form;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

const GO_TO_STEP_2 = 'gotostep2';

if (!isset($_SESSION['form'])) {
    $_SESSION['form'] = new Form();
}

// Type de sondage
if (isset($_GET['type']) && $_GET['type'] == 'date' ||
    isset($_POST['type']) && $_POST['type'] == 'date'
) {
    $poll_type = 'date';
    $_SESSION['form']->choix_sondage = $poll_type;
} else {
    $poll_type = 'classic';
    $_SESSION['form']->choix_sondage = $poll_type;
}

// We clean the data
$goToStep2 = filter_input(INPUT_POST, GO_TO_STEP_2, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(date|classic)$/']]);
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

//
if (!empty($_POST[GO_TO_STEP_2])) {
    $_SESSION['form']->title = $title;
    $_SESSION['form']->admin_name = $name;
    $_SESSION['form']->admin_mail = $mail;
    $_SESSION['form']->description = $description;
    $_SESSION['form']->editable = ($editable !== null);
    $_SESSION['form']->receiveNewVotes = ($receiveNewVotes !== null);
    $_SESSION['form']->receiveNewComments = ($receiveNewComments !== null);

    if ($config['use_smtp'] == true) {
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

    if ($title && $name && $email_OK && !$error_on_title && !$error_on_description && !$error_on_name) {

        if ($goToStep2 == 'date') {
            header('Location:create_date_poll.php');
            exit();
        }

        if ($goToStep2 == 'classic') {
            header('Location:create_classic_poll.php');
            exit();
        }

    } else {
        // Title Erreur !
        $title = __('Generic', 'Error!') . ' - ' . __('Step 1', 'Poll creation (1 on 3)');
    }
} else {
    // Title OK (formulaire pas encore rempli)
    $title = __('Step 1', 'Poll creation (1 on 3)');
}

// Prepare error messages
$errors = array(
    'title' => array(
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'description' => array(
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'name' => array(
        'msg' => '',
        'aria' => '',
        'class' => ''
    ),
    'email' => array(
        'msg' => '',
        'aria' => '',
        'class' => ''
    )
);

// TODO Move it to TPL
if (!empty($_POST[GO_TO_STEP_2])) {
    if (empty($_POST['title'])) {
        $errors['title']['aria'] = 'aria-describeby="poll_title_error" ';
        $errors['title']['class'] = ' has-error';
        $errors['title']['msg'] = __('Error', 'Enter a title');
    } elseif ($error_on_title) {
        $errors['title']['aria'] = 'aria-describeby="poll_title_error" ';
        $errors['title']['class'] = ' has-error';
        $errors['title']['msg'] = __('Error', 'Something is wrong with the format');
    }

    if ($error_on_description) {
        $errors['description']['aria'] = 'aria-describeby="poll_comment_error" ';
        $errors['description']['class'] = ' has-error';
        $errors['description']['msg'] = __('Error', 'Something is wrong with the format');
    }

    if (empty($_POST['name'])) {
        $errors['name']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['name']['class'] = ' has-error';
        $errors['name']['msg'] = __('Error', 'Enter a name');
    } elseif ($error_on_name) {
        $errors['name']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['name']['class'] = ' has-error';
        $errors['name']['msg'] = __('Error', 'Something is wrong with the format');
    }

    if (empty($_POST['mail'])) {
        $errors['email']['aria'] = 'aria-describeby="poll_name_error" ';
        $errors['email']['class'] = ' has-error';
        $errors['email']['msg'] = __('Error', 'Enter an email address');
    } elseif ($error_on_mail) {
        $errors['email']['aria'] = 'aria-describeby="poll_email_error" ';
        $errors['email']['class'] = ' has-error';
        $errors['email']['msg'] = __('Error', 'The address is not correct! You should enter a valid email address (like r.stallman@outlock.com) in order to receive the link to your poll.');
    }
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


$useRemoteUser = USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']);

$smarty->assign('title', $title);
$smarty->assign('useRemoteUser', $useRemoteUser);
$smarty->assign('errors', $errors);
$smarty->assign('use_smtp', $config['use_smtp']);
$smarty->assign('goToStep2', GO_TO_STEP_2);

$smarty->assign('poll_type', $poll_type);
$smarty->assign('poll_title', Utils::fromPostOrEmpty('title'));
$smarty->assign('poll_description', Utils::fromPostOrEmpty('description'));
$smarty->assign('poll_name', Utils::fromPostOrEmpty('name'));
$smarty->assign('poll_mail', Utils::fromPostOrEmpty('mail'));
$smarty->assign('poll_editable', Utils::fromPostOrEmpty('editable'));
$smarty->assign('poll_receiveNewVotes', Utils::fromPostOrEmpty('receiveNewVotes'));
$smarty->assign('poll_receiveNewComments', Utils::fromPostOrEmpty('receiveNewComments'));
$smarty->assign('form', $_SESSION['form']);

$smarty->display('create_poll.tpl');
