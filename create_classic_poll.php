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
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Services\SessionService;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

/* Service */
/*---------*/
$logService = new LogService();
$pollService = new PollService($connect, $logService);
$mailService = new MailService($config['use_smtp'], $config['smtp_options']);
$purgeService = new PurgeService($connect, $logService);
$sessionService = new SessionService();

if (is_file('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Min/Max archive date
$min_expiry_time = $pollService->minExpiryDate();
$max_expiry_time = $pollService->maxExpiryDate();

$form = unserialize($_SESSION['form']);

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
} elseif (empty($_POST['fin_sondage_autre']) ) {
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
        $smarty->assign('title', __('Step 2 classic', 'Poll subjects (2 on 3)'));
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

        // Expiration date is initialised with config parameter. Value will be modified in step 4 if user has defined an other date
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

        $end_date_str = utf8_encode(strftime($date_format['txt_date'], $max_expiry_time)); //textual date

        $_SESSION['form'] = serialize($form);

        $smarty->assign('title', __('Step 3', 'Removal date and confirmation (3 on 3)'));
        $smarty->assign('summary', $summary);
        $smarty->assign('end_date_str', $end_date_str);
        $smarty->assign('default_poll_duration', $config['default_poll_duration']);
        $smarty->assign('use_smtp', $config['use_smtp']);

        $smarty->display('create_poll_step_3.tpl');
        exit;
    case 4: // Step 4 : Data prepare before insert in DB
        $enddate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);

        if (!empty($enddate)) {
            $registredate = explode('/', $enddate);

            if (is_array($registredate) && count($registredate) === 3) {
                $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

                if ($time < $min_expiry_time) {
                    $form->end_date = $min_expiry_time;
                } elseif ($max_expiry_time < $time) {
                    $form->end_date = $max_expiry_time;
                } else {
                    $form->end_date = $time;
                }
            }
        }

        if (empty($form->end_date)) {
            // By default, expiration date is 6 months after last day
            $form->end_date = $max_expiry_time;
        }

        // Insert poll in database
        $ids = $pollService->createPoll($form);
        $poll_id = $ids[0];
        $admin_poll_id = $ids[1];

        // Send confirmation by mail if enabled
        if ($config['use_smtp'] === true) {
            $message = __('Mail', "This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
            $message .= '<br/><br/>';
            $message .= Utils::htmlMailEscape($form->admin_name) . ' ' . __('Mail', 'hast just created a poll called') . ' : "' . Utils::htmlMailEscape($form->title) . '".<br/>';
            $message .= sprintf(__('Mail', 'Thanks for filling the poll at the link above') . ' :<br/><br/><a href="%1$s">%1$s</a>', Utils::getUrlSondage($poll_id));

            $message_admin = __('Mail', "This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
            $message_admin .= sprintf(' :<br/><br/><a href="%1$s">%1$s</a>', Utils::getUrlSondage($admin_poll_id, true));

            if ($mailService->isValidEmail($form->admin_mail)) {
                $mailService->send($form->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'Author\'s message') . '] ' . __('Generic', 'Poll') . ': ' . $form->title, $message_admin);
                $mailService->send($form->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'For sending to the polled users') . '] ' . __('Generic', 'Poll') . ': ' . $form->title, $message);
            }
        }

        // Clean Form data in $_SESSION
        unset($_SESSION['form']);

        // Delete old polls
        $purgeService->purgeOldPolls();

        // creation message
        $sessionService->set("Framadate", "messagePollCreated", TRUE);
        // Redirect to poll administration
        header('Location:' . Utils::getUrlSondage($admin_poll_id, true));
        exit;

    case 1: // Step 1/4 : error if $_SESSION from info_sondage are not valid
    default:
        $smarty->assign('title', __('Error', 'Error!'));
        $smarty->assign('error', __('Error', 'You haven\'t filled the first section of the poll creation.'));
        $smarty->display('error.tpl');
        exit;
}
