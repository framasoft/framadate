<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft https://git.framasoft.org/framasoft/framadate/)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft https://git.framasoft.org/framasoft/framadate/)
 */
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\MailService;
use Framadate\Services\PurgeService;
use Framadate\Utils;
use Framadate\Choice;

include_once __DIR__ . '/app/inc/init.php';

/* Service */
/*---------*/
$logService = new LogService();
$pollService = new PollService($connect, $logService);
$mailService = new MailService($config['use_smtp']);
$purgeService = new PurgeService($connect, $logService);
$inputService = new InputService();

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Step 1/4 : error if $_SESSION from info_sondage are not valid
if (!isset($_SESSION['form']->title) || !isset($_SESSION['form']->admin_name) || ($config['use_smtp'] && !isset($_SESSION['form']->admin_mail))) {

    $smarty->assign('title', __('Error', 'Error!'));
    $smarty->assign('error', __('Error', 'You haven\'t filled the first section of the poll creation.'));
    $smarty->display('error.tpl');

} else {
    // Min/Max archive date
    $min_expiry_time = $pollService->minExpiryDate();
    $max_expiry_time = $pollService->maxExpiryDate();

    // The poll format is DATE
    if ($_SESSION['form']->format !== 'D') {
        $_SESSION['form']->format = 'D';
        $_SESSION['form']->clearChoices();
    }

    // Step 4 : Data prepare before insert in DB
    if (!empty($_POST['confirmation'])) {

        // Define expiration date
        $enddate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);


        if (!empty($enddate)) {
            $registredate = explode('/', $enddate);

            if (is_array($registredate) && count($registredate) == 3) {
                $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

                if ($time < $min_expiry_time) {
                    $_SESSION['form']->end_date = $min_expiry_time;
                } elseif ($max_expiry_time < $time) {
                    $_SESSION['form']->end_date = $max_expiry_time;
                } else {
                    $_SESSION['form']->end_date = $time;
                }
            }
        }

        if (empty($_SESSION['form']->end_date)) {
            // By default, expiration date is 6 months after last day
            $_SESSION['form']->end_date = $max_expiry_time;
        }

        // Insert poll in database
        $ids = $pollService->createPoll($_SESSION['form']);
        $poll_id = $ids[0];
        $admin_poll_id = $ids[1];


        // Send confirmation by mail if enabled
        if ($config['use_smtp'] === true) {
            $message = __('Mail', "This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
            $message .= '<br/><br/>';
            $message .= Utils::htmlEscape($_SESSION['form']->admin_name) . ' ' . __('Mail', 'hast just created a poll called') . ' : "' . Utils::htmlEscape($_SESSION['form']->title) . '".<br/>';
            $message .= __('Mail', 'Thanks for filling the poll at the link above') . ' :<br/><br/><a href="%1$s">%1$s</a>';

            $message_admin = __('Mail', "This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
            $message_admin .= ' :<br/><br/><a href="%1$s">%1$s</a>';

            $message = sprintf($message, Utils::getUrlSondage($poll_id));
            $message_admin = sprintf($message_admin, Utils::getUrlSondage($admin_poll_id, true));

            if ($mailService->isValidEmail($_SESSION['form']->admin_mail)) {
                $mailService->send($_SESSION['form']->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'Author\'s message') . '] ' . __('Generic', 'Poll') . ': ' . Utils::htmlEscape($_SESSION['form']->title), $message_admin);
                $mailService->send($_SESSION['form']->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'For sending to the polled users') . '] ' . __('Generic', 'Poll') . ': ' . Utils::htmlEscape($_SESSION['form']->title), $message);
            }
        }

        // Clean Form data in $_SESSION
        unset($_SESSION['form']);

        // Delete old polls
        $purgeService->purgeOldPolls();

        // Redirect to poll administration
        header('Location:' . Utils::getUrlSondage($admin_poll_id, true));
        exit;

    } else {

        if (!empty($_POST['days'])) {

            // Clear previous choices
            $_SESSION['form']->clearChoices();

            for ($i = 0; $i < count($_POST['days']); $i++) {
                $day = $_POST['days'][$i];

                if (!empty($day)) {
                    // Add choice to Form data
                    $time = mktime(0, 0, 0, substr($_POST["days"][$i],3,2),substr($_POST["days"][$i],0,2),substr($_POST["days"][$i],6,4));
                    $choice = new Choice($time);
                    $_SESSION['form']->addChoice($choice);

                    $schedules = $inputService->filterArray($_POST['horaires'.$i], FILTER_DEFAULT);
                    for($j = 0; $j < count($schedules); $j++) {
                        if (!empty($schedules[$j])) {
                            $choice->addSlot(strip_tags($schedules[$j]));
                        }
                    }
                }
            }
        }
    }

    // Step 3/4 : Confirm poll creation
    if (!empty($_POST['choixheures']) && !isset($_SESSION['form']->totalchoixjour)) {

        Utils::print_header ( __('Step 3', 'Removal date and confirmation (3 on 3)') );
        bandeau_titre(__('Step 3', 'Removal date and confirmation (3 on 3)'));

        $end_date_str = utf8_encode(strftime('%d/%m/%Y', $max_expiry_time)); // textual date

        // Summary
        $summary = '<ul>';
        $choices = $_SESSION['form']->getChoices();
        foreach ($choices as $choice) {
            $summary .= '<li>'.strftime($date_format['txt_full'], $choice->getName());
            $first = true;
            foreach ($choice->getSlots() as $slots) {
                $summary .= $first ? ': ' : ', ';
                $summary .= $slots;
                    $first = false;
            }
            $summary .= '</li>';
        }
        $summary .= '</ul>';


        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'create_date_poll.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
        <div class="col-md-8 col-md-offset-2">
            <h3>'. __('Step 3', 'Confirm the creation of your poll') .'</h3>
            <div class="well summary">
                <h4>'. __('Step 3', 'List of your choices').'</h4>
                '. $summary .'
            </div>
            <div class="alert alert-info clearfix">
                <p>' . __f('Step 3', 'Your poll will be automatically archived in %d days.', $config['default_poll_duration']) . '
                <br />' . __('Step 3', 'You can set a closer archiving date for it.') .'</p>
                <div class="form-group">
                    <label for="enddate" class="col-sm-5 control-label">'. __('Step 3', 'Archiving date:') .'</label>
                    <div class="col-sm-6">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="enddate" data-date-format="'. __('Date', 'dd/mm/yyyy') .'" aria-describedby="dateformat" name="enddate" value="'.$end_date_str.'" size="10" maxlength="10" placeholder="'. __('Date', 'dd/mm/yyyy') .'" />
                        </div>
                    </div>
                    <span id="dateformat" class="sr-only">('. __('Date', 'dd/mm/yyyy') .')</span>
                </div>
            </div>
            <div class="alert alert-warning">
                <p>'. __('Step 3', 'Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.'). '</p>';
        if($config['use_smtp'] == true) {
            echo '<p>' . __('Step 3', 'Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.') .'</p>';
        }
        echo '
            </div>
            <p class="text-right">
                <button class="btn btn-default" onclick="javascript:window.history.back();" title="'. __('Step 3', 'Back to step 2') . '">'. __('Generic', 'Back') . '</button>
                <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">'. __('Step 3', 'Create the poll') . '</button>
            </p>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    // Step 2/4 : Select dates of the poll
    } else {

        // Prefill form->choices
        foreach ($_SESSION['form']->getChoices() as $c) {
            $count = 3 - count($c->getSlots());
            for($i=0; $i< $count; $i++) {
                $c->addSlot('');
            }
        }

        $count = 3 - count($_SESSION['form']->getChoices());
        for($i=0; $i< $count; $i++) {
            $c = new Choice('');
            $c->addSlot('');
            $c->addSlot('');
            $c->addSlot('');
            $_SESSION['form']->addChoice($c);
        }

        // Display step 2
        $smarty->assign('title', __('Step 2 date', 'Poll dates (2 on 3)'));
        $smarty->assign('choices', $_SESSION['form']->getChoices());

        $smarty->display('create_date_poll_step_2.tpl');

    }
}
