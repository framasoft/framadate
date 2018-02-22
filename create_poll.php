<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha�l DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est r�gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

use Framadate\Form;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Security\PasswordHasher;
use Framadate\Services\InputService;
use Framadate\Utils;
use JMS\Serializer\SerializerBuilder;

include_once __DIR__ . '/app/inc/init.php';

const GO_TO_STEP_2 = 'gotostep2';

/* Services */
/*----------*/

$inputService = new InputService();
$pollRepository = RepositoryFactory::pollRepository();

/* PAGE */
/* ---- */

$serializer = SerializerBuilder::create()->build();
if (isset($_SESSION['form'])) {
    /** @var Form $form */
    $form = $serializer->deserialize($_SESSION['form'], Form::class, 'json');
} else {
    $form = new Form();
}


// Type de sondage
if (isset($_GET['type']) && $_GET['type'] === 'date' ||
    isset($_POST['type']) && $_POST['type'] === 'date'
) {
    $poll_type = 'date';
    $form->choix_sondage = $poll_type;
} else {
    $poll_type = 'classic';
    $form->choix_sondage = $poll_type;
}

// We clean the data
$goToStep2 = filter_input(INPUT_POST, GO_TO_STEP_2, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^(date|classic)$/']]);
if ($goToStep2) {
    $title = $inputService->filterTitle($_POST['title']);

    $use_ValueMax = isset($_POST['use_ValueMax']) ? $inputService->filterBoolean($_POST['use_ValueMax']) : false;
    $ValueMax = $use_ValueMax === true ? $inputService->filterInteger($_POST['ValueMax']) : null;

    $use_customized_url = isset($_POST['use_customized_url']) ? $inputService->filterBoolean($_POST['use_customized_url']) : false;
    $customized_url = $use_customized_url === true ? $inputService->filterId($_POST['customized_url']) : null;
    $name = $inputService->filterName($_POST['name']);
    $mail = $config['use_smtp'] === true ? $inputService->filterMail($_POST['mail']) : null;
    $description = $inputService->filterDescription($_POST['description']);
    $editable = $inputService->filterEditable($_POST['editable']);
    $receiveNewVotes = isset($_POST['receiveNewVotes']) ? $inputService->filterBoolean($_POST['receiveNewVotes']) : false;
    $receiveNewComments = isset($_POST['receiveNewComments']) ? $inputService->filterBoolean($_POST['receiveNewComments']) : false;
    $hidden = isset($_POST['hidden']) ? $inputService->filterBoolean($_POST['hidden']) : false;
    $use_password = filter_input(INPUT_POST, 'use_password', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $password_repeat = isset($_POST['password_repeat']) ? $_POST['password_repeat'] : null;
    $results_publicly_visible = filter_input(INPUT_POST, 'results_publicly_visible', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);

    // On initialise également les autres variables
    $error_on_mail = false;
    $error_on_title = false;
    $error_on_name = false;
    $error_on_description = false;
    $error_on_password = false;
    $error_on_password_repeat = false;
    $error_on_customized_url = false;
    $error_on_ValueMax = false;

    $form->title = $title;
    $form->id = $customized_url;
    $form->use_customized_url = $use_customized_url;
    $form->use_ValueMax = $use_ValueMax;
    $form->ValueMax = $ValueMax;
    $form->admin_name = $name;
    $form->admin_mail = $mail;
    $form->description = $description;
    $form->setEditable($editable);
    $form->receiveNewVotes = $receiveNewVotes;
    $form->receiveNewComments = $receiveNewComments;
    $form->hidden = $hidden;
    $form->use_password = ($use_password !== null);
    $form->results_publicly_visible = ($results_publicly_visible !== null);

    if ($config['use_smtp'] === true) {
        if (empty($mail)) {
            $error_on_mail = true;
        }
    }

    if ($title !== $_POST['title']) {
        $error_on_title = true;
    }

    if ($use_customized_url) {
        if ($customized_url === false) {
            $error_on_customized_url = true;
        } else if ($pollRepository->existsById($customized_url)) {
            $error_on_customized_url = true;
            $error_on_customized_url_msg = __('Error', 'Poll id already used');
        }
    }

	if ($use_ValueMax) {
		if ($use_ValueMax === false) {
            $error_on_ValueMax = true;
			$error_on_customized_url_msg = __('Error', 'Mauvaise valeur');
        }
	}

    if ($name !== $_POST['name']) {
        $error_on_name = true;
    }

    if ($description === false) {
        $error_on_description = true;
    }

    // Si pas d'erreur dans l'adresse alors on change de page vers date ou autre
    if ($config['use_smtp'] === true) {
        $email_OK = $mail && !$error_on_mail;
    } else {
        $email_OK = true;
    }

    if ($use_password) {
        if (empty($password)) {
            $error_on_password = true;
        } else if ($password !== $password_repeat) {
            $error_on_password_repeat = true;
        }
    }

    if ($title && $name && $email_OK && !$error_on_title && !$error_on_customized_url && !$error_on_description && !$error_on_name
        && !$error_on_password && !$error_on_password_repeat &&!$error_on_ValueMax
    ) {
        // If no errors, we hash the password if needed
        if ($form->use_password) {
            $form->password_hash = PasswordHasher::hash($password);
        } else {
            $form->password_hash = null;
            $form->results_publicly_visible = null;
        }

        $_SESSION['form'] = $serializer->serialize($form, 'json');

        if ($goToStep2 === 'date') {
            header('Location:create_date_poll.php');
            exit();
        }

        if ($goToStep2 === 'classic') {
            header('Location:create_classic_poll.php');
            exit();
        }
    } else {
        // Title Erreur !
        $title = __('Error', 'Error!') . ' - ' . __('Step 1', 'Poll creation (1 on 3)');
    }
} else {
    // Title OK (formulaire pas encore rempli)
    $title = __('Step 1', 'Poll creation (1 on 3)');
}

// Prepare error messages
$errors = [
    'title' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'customized_url' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'description' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'name' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'email' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'password' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
	'ValueMax' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
    'password_repeat' => [
        'msg' => '',
        'aria' => '',
        'class' => ''
    ],
];

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

    if ($error_on_customized_url) {
        $errors['customized_url']['aria'] = 'aria-describeby="customized_url" ';
        $errors['customized_url']['class'] = ' has-error';
        $errors['customized_url']['msg'] = isset($error_on_customized_url_msg) ? $error_on_customized_url_msg : __('Error', 'Something is wrong with the format');
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

    if ($error_on_password) {
        $errors['password']['aria'] = 'aria-describeby="poll_password_error" ';
        $errors['password']['class'] = ' has-error';
        $errors['password']['msg'] = __('Error', 'Password is empty');
    }
    if ($error_on_password_repeat) {
        $errors['password_repeat']['aria'] = 'aria-describeby="poll_password_repeat_error" ';
        $errors['password_repeat']['class'] = ' has-error';
        $errors['password_repeat']['msg'] = __('Error', 'Passwords do not match');
    }
	if ($error_on_ValueMax) {
        $errors['ValueMax']['aria'] = 'aria-describeby="poll_ValueMax" ';
        $errors['ValueMax']['class'] = ' has-error';
        $errors['ValueMax']['msg'] = __('Error', 'error on ValueMax');
    }
}

$useRemoteUser = USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']);
$_SESSION['form'] = $serializer->serialize($form, 'json');

echo $twig->render('create_poll.twig', [
    'title' => $title,
    'useRemoteUser' => $useRemoteUser,
    'errors' => $errors,
    'use_smtp' => $config['use_smtp'],
    'default_to_marldown_editor' => $config['markdown_editor_by_default'],
    'goToStep2' => GO_TO_STEP_2,
    'poll_type' => $poll_type,
    'poll_title' => Utils::fromPostOrDefault('title', $form->title),
    'customized_url' => Utils::fromPostOrDefault('customized_url', $form->id),
    'use_customized_url' => Utils::fromPostOrDefault('use_customized_url', $form->use_customized_url),
    'ValueMax' => Utils::fromPostOrDefault('ValueMax', $form->ValueMax),
    'use_ValueMax' => Utils::fromPostOrDefault('use_ValueMax', $form->use_ValueMax),
    'poll_description' => !empty($_POST['description']) ? $_POST['description'] :  $form->description,
    'poll_name' => Utils::fromPostOrDefault('name', $form->admin_name),
    'poll_mail' => Utils::fromPostOrDefault('mail', $form->admin_mail),
    'poll_editable' => Utils::fromPostOrDefault('editable', $form->editable),
    'poll_receiveNewVotes' => Utils::fromPostOrDefault('receiveNewVotes', $form->receiveNewVotes),
    'poll_receiveNewComments' => Utils::fromPostOrDefault('receiveNewComments', $form->receiveNewComments),
    'poll_hidden' => Utils::fromPostOrDefault('hidden', $form->hidden),
    'poll_use_password' => Utils::fromPostOrDefault('use_password', $form->use_password),
    'poll_results_publicly_visible' => Utils::fromPostOrDefault('results_publicly_visible', $form->results_publicly_visible),
    'form' => $form,
]);
