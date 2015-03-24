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

    Utils::print_header ( __('Error\\Error!') );
    bandeau_titre(__('Error\\Error!'));

    echo '
    <div class="alert alter-danger">
        <h3>' . __('Error\\You haven\'t filled the first section of the poll creation.') . ' !</h3>
        <p>' . __('Error\\Back to the homepage of') . ' ' . '<a href="' . Utils::get_server_name() . '">' . NOMAPPLICATION . '</a>.</p>
    </div>';


    bandeau_pied();

} else {
    $min_time = time() + 86400;
    $max_time = time() + (86400 * $config['default_poll_duration']);

    // Step 4 : Data prepare before insert in DB
    if (!empty($_POST['confirmation'])) {

        // Define expiration date
        $enddate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);

        if (!empty($enddate)) {
            $registredate = explode('/', $enddate);

            if (is_array($registredate) && count($registredate) == 3) {
                $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

                if ($time < $min_time) {
                    $_SESSION['form']->end_date = $min_time;
                } elseif ($max_time < $time) {
                    $_SESSION['form']->end_date = $max_time;
                } else {
                    $_SESSION['form']->end_date = $time;
                }
            }
        }

        if (empty($_SESSION['form']->end_date)) {
            // By default, expiration date is 6 months after last day
            $_SESSION['form']->end_date = $max_time;
        }

        // Insert poll in database
        $ids = $pollService->createPoll($_SESSION['form']);
        $poll_id = $ids[0];
        $admin_poll_id = $ids[1];


        // Send confirmation by mail if enabled
        if ($config['use_smtp'] === true) {
            $message = __("Mail\\This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
            $message .= "\n\n";
            $message .= stripslashes(html_entity_decode($_SESSION['form']->admin_name, ENT_QUOTES, 'UTF-8')) . ' ' . __('Mail\\hast just created a poll called') . ' : "' . stripslashes(htmlspecialchars_decode($_SESSION['form']->title, ENT_QUOTES)) . "\".\n";
            $message .= __('Mail\\Thanks for filling the poll at the link above') . " :\n\n%s\n\n" . __('Mail\\Thanks for your confidence.') . "\n" . NOMAPPLICATION;

            $message_admin = __("Mail\\This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
            $message_admin .= " :\n\n" . "%s \n\n" . __('Mail\\Thanks for your confidence.') . "\n" . NOMAPPLICATION;

            $message = sprintf($message, Utils::getUrlSondage($poll_id));
            $message_admin = sprintf($message_admin, Utils::getUrlSondage($admin_poll_id, true));

            if ($mailService->isValidEmail($_SESSION['form']->admin_mail)) {
                $mailService->send($_SESSION['form']->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail\\Author\'s message') . '] ' . __('Generic\\Poll') . ' : ' . stripslashes(htmlspecialchars_decode($_SESSION['form']->title, ENT_QUOTES)), $message_admin);
                $mailService->send($_SESSION['form']->admin_mail, '[' . NOMAPPLICATION . '][' . __('Mail\\For sending to the polled users') . '] ' . __('Generic\\Poll') . ' : ' . stripslashes(htmlspecialchars_decode($_SESSION['form']->title, ENT_QUOTES)), $message);
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

    //le format du sondage est DATE
    $_SESSION['form']->format = 'D';

    // Step 3/4 : Confirm poll creation
    if (!empty($_POST['choixheures']) && !isset($_SESSION['form']->totalchoixjour)) {

        Utils::print_header ( __('Step 3\\Removal date and confirmation (3 on 3)') );
        bandeau_titre(__('Step 3\\Removal date and confirmation (3 on 3)'));

        $_SESSION['form']->sortChoices();
        $last_date = $_SESSION['form']->lastChoice()->getName();
        $removal_date = $last_date + (86400 * $config['default_poll_duration']);

        // Summary
        $summary = '<ul>';
        foreach ($_SESSION['form']->getChoices() as $choice) {
            $summary .= '<li>'.strftime($date_format['txt_full'], $choice->getName());
            $first = true;
            foreach ($choice->getSlots() as $slots) {
                $summary .= $first ? ' : ' : ', ';
                $summary .= $slots;
                    $first = false;
            }
            $summary .= '</li>';
        }
        $summary .= '</ul>';

        $end_date_str = utf8_encode(strftime('%d/%m/%Y', $max_time)); //textual date

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
        <div class="col-md-8 col-md-offset-2">
            <h3>'. __('Step 3\\Confirm the creation of your poll') .'</h3>
            <div class="well summary">
                <h4>'. __('Step 3\\List of your choices').'</h4>
                '. $summary .'
            </div>
            <div class="alert alert-info clearfix">
                <p>' . __('Step 3\\Your poll will be automatically removed '). $config['default_poll_duration'] . ' ' . __('Generic\\days') . ' ' .__('Step 3\\after the last date of your poll:') . '<br />' . __('Step 3\\You can set a closer removal date for it.') .'</p>
                <div class="form-group">
                    <label for="enddate" class="col-sm-5 control-label">'. __('Step 3\\Removal date:') .'</label>
                    <div class="col-sm-6">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="enddate" data-date-format="'. __('Date\\dd/mm/yyyy') .'" aria-describedby="dateformat" name="enddate" value="'.$end_date_str.'" size="10" maxlength="10" placeholder="'. __('dd/mm/yyyy') .'" />
                        </div>
                    </div>
                    <span id="dateformat" class="sr-only">('. __("Date\\dd/mm/yyyy") .')</span>
                </div>
            </div>
            <div class="alert alert-warning">
                <p>'. __('Step 3\\Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.'). '</p>';
        if($config['use_smtp'] == true) {
            echo '<p>' . __('Step 3\\Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.') .'</p>';
        }
        echo '
            </div>
            <p class="text-right">
                <button class="btn btn-default" onclick="javascript:window.history.back();" title="'. __('Step 3\\Back to step 2') . '">'. __('Generic\\Back') . '</button>
                <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">'. __('Step 3\\Create the poll') . '</button>
            </p>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    // Step 2/4 : Select dates of the poll
    } else {
        Utils::print_header(__('Step 2 date\\Poll dates (2 on 3)'));
        bandeau_titre(__('Step 2 date\\Poll dates (2 on 3)'));

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
        <div class="col-md-10 col-md-offset-1">
            <h3>'. __('Step 2 date\\Choose the dates of your poll') .'</h3>
            <div class="alert alert-info">
                <p>'. __('Step 2 date\\To schedule an event you need to propose at least two choices (two hours for one day or two days).').'</p>
                <p>'. __('Step 2 date\\You can add or remove additionnal days and hours with the buttons') .' <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. __('Remove') .'</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. __('Add') .'</span></p>
                <p>'. __('Step 2 date\\For each selected day, you can choose, or not, meeting hours (e.g.: "8h", "8:30", "8h-10h", "evening", etc.)').'</p>
            </div>';

        // Fields days : 3 by default
        $nb_days = (isset($_SESSION['totalchoixjour'])) ? count($_SESSION['totalchoixjour']) : 3;
        for ($i = 0; $i < $nb_days; $i++) {
            $day_value = isset($_SESSION['totalchoixjour'][$i]) ? strftime('%d/%m/%Y', $_SESSION['totalchoixjour'][$i]) : '';
            echo '
            <fieldset>
                <div class="form-group">
                    <legend>
                        <label class="sr-only" for="day'.$i.'">'. __('Generic\\Day') .' '. ($i+1) .'</label>
                        <div class="input-group date col-xs-7">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="day'.$i.'" title="'. __('Generic\\Day') .' '. ($i+1) .'" data-date-format="'. __('Date\\dd/mm/yyyy') .'" aria-describedby="dateformat'.$i.'" name="days[]" value="'.$day_value.'" size="10" maxlength="10" placeholder="'. __('Date\\dd/mm/yyyy') .'" />
                        </div>
                        <span id="dateformat'.$i.'" class="sr-only">('. __('Date\\dd/mm/yyyy') .')</span>
                    </legend>'."\n";

            // Fields hours : 3 by default
            $moments = isset($_SESSION['horaires' . $i]) ? $_SESSION['horaires' . $i] : [];
            for ($j = 0; $j < max(count($moments), 3); $j++) {
                $hour_value = isset($_SESSION['horaires'.$i][$j]) ? $_SESSION['horaires'.$i][$j] : '';
                echo '
                    <div class="col-sm-2">
                        <label for="d'.$i.'-h'.$j.'" class="sr-only control-label">'. __('Generic\\Time') .' '. ($j+1) .'</label>
                        <input type="text" class="form-control hours" title="'.$day_value.' - '. __('Generic\\Time') .' '. ($j+1) .'" placeholder="'. __('Generic\\Time') .' '. ($j+1) .'" id="d'.$i.'-h'.$j.'" name="horaires'.$i.'[]" value="'.$hour_value.'" />
                    </div>'."\n";
            }
            echo '
                    <div class="col-sm-2"><div class="btn-group btn-group-xs" style="margin-top: 5px;">
                        <button type="button" title="'. __('Step 2 date\\Remove an hour') .'" class="remove-an-hour btn btn-default"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. __("Remove an hour") .'</span></button>
                        <button type="button" title="'. __('Step 2 date\\Add an hour') .'" class="add-an-hour btn btn-default"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. __("Add an hour") .'</span></button>
                    </div></div>
                </div>
            </fieldset>';
            }
        echo '
            <div class="col-md-4">
                <button type="button" id="copyhours" class="btn btn-default disabled" title="'. __('Step 2 date\\Copy hours of the first day') .'"><span class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span><span class="sr-only">'. __('Step 2 date\\Copy hours of the first day') .'</span></button>
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-day" class="btn btn-default disabled" title="'. __('Step 2 date\\Remove a day') .'"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. __('Step 2 date\\Remove a day') .'</span></button>
                    <button type="button" id="add-a-day" class="btn btn-default" title="'. __('Step 2 date\\Add a day') .'"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. __('Step 2 date\\Add a day') .'</span></button>
                </div>
            </div>
            <div class="col-md-8 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-remove text-danger"></span> '. __('Generic\\Remove') . ' <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a id="resetdays" href="javascript:void(0)">'. __('Step 2 date\\Remove all days') .'</a></li>
                        <li><a id="resethours" href="javascript:void(0)">'. __('Step 2 date\\Remove all hours') .'</a></li>
                    </ul>
                </div>
                <a class="btn btn-default" href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=date" title="'. __('Step 2\\Back to step 1') . '">'. __('Generic\\Back') . '</a>
                <button name="choixheures" value="'. __('Generic\\Next') .'" type="submit" class="btn btn-success disabled" title="'. __('Step 2\\Go to step 3') . '">'. __('Generic\\Next') .'</button>
            </div>
        </div>
    </div>
    </form>

    <script type="text/javascript" src="js/app/framadatepicker.js"></script>
    <script type="text/javascript" src="js/app/date_poll.js"></script>
    '."\n";

        bandeau_pied();

    }
}
