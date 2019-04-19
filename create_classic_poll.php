<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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
use Framadate\Choice;
use Framadate\Form;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

$pollService = Services::poll();

if (is_file('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once 'bandeaux.php';
}

$max_expiry_time = $pollService->maxExpiryDate();

$form = isset($_SESSION['form']) ? unserialize($_SESSION['form']) : null;

if ($form === null || !($form instanceof Form)) {
    $smarty->assign('title', __('Error', 'Error!'));
    $smarty->assign('error', __('Error', 'You haven\'t filled the first section of the poll creation, or your session has expired.'));
    $smarty->display('error.tpl');
    exit;
}

// The poll format is AUTRE (other) if we are in this file
if (!isset($form->format)) {
    $form->format = 'A';
}

// The poll format is AUTRE (other)
if ($form->format !== 'A') {
    $form->format = 'A';
    $form->clearChoices();
}

if (!isset($form->title) || !isset($form->admin_name) || ($config['use_smtp'] && !isset($form->admin_mail))) {
    $step = 1;
} elseif (isset($_POST['confirmation'])) {
    $step = 4;
} elseif (empty($form->errors) && empty($_POST['fin_sondage_autre']) ) {
    $step = 2;
} else {
    $step = 3;
}

switch ($step) {
    case 2: // Step 2/4 : Select choices of the poll
        $choices = $form->getChoices();
        $nb_choices = max( 5- count($choices), 0);
        while ($nb_choices-- > 0) {
            $c = new Choice('');
            $form->addChoice($c);
        }

        $_SESSION['form'] = serialize($form);

        // Display step 2
        $smarty->assign('title', __('Step 2 classic', 'Poll options (2 of 3)'));
        $smarty->assign('choices', $form->getChoices());
        $smarty->assign('allowMarkdown', $config['user_can_add_img_or_link']);
        $smarty->assign('error', null);

        $smarty->display('create_classic_poll_step_2.tpl');
        exit;

    case 3: // Step 3/4 : Confirm poll creation and choose a removal date
        // Handle Step2 submission
        if (!empty($_POST['choices'])) {
            // remove empty choices
            $_POST['choices'] = array_filter($_POST['choices'], function ($c) {
                return !empty($c);
            });

            $form->clearChoices();

            // store choices in $_SESSION
            foreach ($_POST['choices'] as $c) {
                $c = strip_tags($c);
                $choice = new Choice($c);
                $form->addChoice($choice);
            }
        }

        // Expiry date is initialised with config parameter. Value will be modified in step 4 if user has defined an other date
        $form->end_date = $max_expiry_time;

        // Summary
        $summary = '<ol>';
        foreach ($form->getChoices() as $i => $choice) {
            /** @var Choice $choice */
            preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $choice->getName(), $md_a_img); // Markdown [![alt](src)](href)
            preg_match_all('/!\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_img); // Markdown ![alt](src)
            preg_match_all('/\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_a); // Markdown [text](href)
            if (isset($md_a_img[2][0]) && $md_a_img[2][0] !== '' && isset($md_a_img[3][0]) && $md_a_img[3][0] !== '') { // [![alt](src)](href)
                $li_subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0] !== '') ? stripslashes($md_a_img[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<a href="' . $md_a_img[3][0] . '"><img src="' . $md_a_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" /></a>';
            } elseif (isset($md_img[2][0]) && $md_img[2][0] !== '') { // ![alt](src)
                $li_subject_text = (isset($md_img[1][0]) && $md_img[1][0] !== '') ? stripslashes($md_img[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<img src="' . $md_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" />';
            } elseif (isset($md_a[2][0]) && $md_a[2][0] !== '') { // [text](href)
                $li_subject_text = (isset($md_a[1][0]) && $md_a[1][0] !== '') ? stripslashes($md_a[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<a href="' . $md_a[2][0] . '">' . $li_subject_text . '</a>';
            } else { // text only
                $li_subject_text = stripslashes($choice->getName());
                $li_subject_html = $li_subject_text;
            }

            $summary .= '<li>' . $li_subject_html . '</li>' . "\n";
        }
        $summary .= '</ol>';

        $end_date_str = date_format_intl($max_expiry_time); //textual date

        $_SESSION['form'] = serialize($form);

        $smarty->assign('title', __('Step 3', 'Removal date and confirmation (3 of 3)'));
        $smarty->assign('summary', $summary);
        $smarty->assign('end_date_str', $end_date_str);
        $smarty->assign('default_poll_duration', $config['default_poll_duration']);
        $smarty->assign('use_smtp', $config['use_smtp']);
        $smarty->assign('errors', $form->errors);

        $smarty->display('create_poll_step_3.tpl');
        exit;

    case 4:
        // Step 4 : Do the poll creation

        // Read expiration date passed in POST parameters
        $end_date = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);

        $admin_poll_id = $pollService->doPollCreation($form, $end_date);

        if (!is_null($admin_poll_id)) {
            // Redirect to poll administration
            header('Location:' . Utils::getUrlSondage($admin_poll_id, true));
        } else {
            // Redirect to current page
            $referer = $_SERVER['HTTP_REFERER'];
            header("Location: $referer");
        }
        exit;

    case 1:
    default:
        // Step 1/4 : error if $_SESSION from info_sondage are not valid
        $smarty->assign('title', __('Error', 'Error!'));
        $smarty->assign('error', __('Error', 'You haven\'t filled the first section of the poll creation, or your session has expired.'));
        $smarty->display('error.tpl');
        exit;
}
