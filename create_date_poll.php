<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft https://framagit.org/framasoft/framadate/)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft https://framagit.org/framasoft/framadate/)
 */
use Framadate\Choice;
use Framadate\Form;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

$inputService = Services::input();
$pollService = Services::poll();

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
}

$max_expiry_time = $pollService->maxExpiryDate();

$form = isset($_SESSION['form']) ? unserialize($_SESSION['form']) : null;

if ($form === null || !($form instanceof Form)) {
    $smarty->assign('title', __('Error', 'Error!'));
    $smarty->assign('error', __('Error', 'You haven\'t filled the first section of the poll creation, or your session has expired.'));
    $smarty->display('error.tpl');
    exit;
}

// The poll format is DATE if we are in this file
if (!isset($form->format)) {
    $form->format = 'D';
}
// If we come from another format, we need to clear choices
if ($form->format !== 'D') {
    $form->format = 'D';
    $form->clearChoices();
}

if (!isset($form->title) || !isset($form->admin_name) || ($config['use_smtp'] && !isset($form->admin_mail))) {
    $step = 1;
} else if (!empty($_POST['confirmation'])) {
    $step = 4;
} else if (empty($form->errors) && (empty($_POST['choixheures']) || isset($form->totalchoixjour))) {
    $step = 2;
} else {
    $step = 3;
}

switch ($step) {
    case 2:
        // Step 2/4 : Select dates of the poll

        // Prefill form->choices
        foreach ($form->getChoices() as $c) {
            /** @var Choice $c */
            $count = 3 - count($c->getSlots());
            for ($i = 0; $i < $count; $i++) {
                $c->addSlot('');
            }
        }

        $count = 3 - count($form->getChoices());
        for ($i = 0; $i < $count; $i++) {
            $c = new Choice('');
            $c->addSlot('');
            $c->addSlot('');
            $c->addSlot('');
            $form->addChoice($c);
        }

        $_SESSION['form'] = serialize($form);

        // Display step 2
        $smarty->assign('title', __('Step 2 date', 'Poll dates (2 of 3)'));
        $smarty->assign('choices', $form->getChoices());
        $smarty->assign('error', null);

        $smarty->display('create_date_poll_step_2.tpl');
        exit;

    case 3:
        // Step 3/4 : Confirm poll creation

        // Handle Step2 submission
        if (!empty($_POST['days'])) {
            // Remove empty dates
            $_POST['days'] = array_filter($_POST['days'], function ($d) {
                return !empty($d);
            });

            // Check if there are at most MAX_SLOTS_PER_POLL slots
            if (count($_POST['days']) > MAX_SLOTS_PER_POLL) {
                // Display step 2
                $smarty->assign('title', __('Step 2 date', 'Poll dates (2 of 3)'));
                $smarty->assign('choices', $form->getChoices());
                $smarty->assign('error', __f('Error', 'You can\'t select more than %d dates', MAX_SLOTS_PER_POLL));

                $smarty->display('create_date_poll_step_2.tpl');
                exit;
            }

            // Clear previous choices
            $form->clearChoices();

            // Reorder moments to deal with suppressed dates
            $moments = [];
            $i = 0;
            while(count($moments) < count($_POST['days'])) {
                if (!empty($_POST['horaires' . $i])) {
                    $moments[] = $_POST['horaires' . $i];
                }
                $i++;
            }

            for ($i = 0; $i < count($_POST['days']); $i++) {
                $day = $_POST['days'][$i];

                if (!empty($day)) {
                    // Add choice to Form data
                    $date = DateTime::createFromFormat(__('Date', 'Y-m-d'), $_POST['days'][$i])->setTime(0, 0, 0);
                    $time = (string) $date->getTimestamp();
                    $choice = new Choice($time);
                    $form->addChoice($choice);

                    $schedules = $inputService->filterArray($moments[$i], FILTER_DEFAULT);
                    for ($j = 0; $j < count($schedules); $j++) {
                        if (!empty($schedules[$j])) {
                            $choice->addSlot(strip_tags($schedules[$j]));
                        }
                    }
                }
            }
            $form->sortChoices();
        }

        // Display step 3
        $summary = '<ul>';
        $choices = $form->getChoices();
        foreach ($choices as $choice) {
            /** @var Choice $choice */
            $date = (new DateTime())->setTimestamp((int) $choice->getName());
            $summary .= '<li>' . $end_date_str = date_format_intl($date); //textual date
            $first = true;
            foreach ($choice->getSlots() as $slots) {
                $summary .= $first ? ': ' : ', ';
                $summary .= $slots;
                $first = false;
            }
            $summary .= '</li>';
        }
        $summary .= '</ul>';

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
