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

if (is_file('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Step 1/4 : error if $_SESSION from info_sondage are not valid
if (empty($_SESSION['form']->title) || empty($_SESSION['form']->admin_name) || (($config['use_smtp']) ? empty($_SESSION['form']->admin_mail) : false)) {

    Utils::print_header(__('Error', 'Error!'));
    bandeau_titre(__('Error', 'Error!'));

    echo '
    <div class="alert alert-danger">
        <h3>' . __('Error', 'You haven\'t filled the first section of the poll creation.') . ' !</h3>
        <p>' . __('Generic', 'Back to the homepage of') . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>' . "\n";

    bandeau_pied();

} else {
    // Min/Max archive date
    $min_expiry_time = $pollService->minExpiryDate();
    $max_expiry_time = $pollService->maxExpiryDate();

    // The poll format is AUTRE (other)
    if ($_SESSION['form']->format !== 'A') {
        $_SESSION['form']->format = 'A';
        $_SESSION['form']->clearChoices();
    }

    // Step 4 : Data prepare before insert in DB
    if (isset($_POST['confirmecreation'])) {

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

    } // Step 3/4 : Confirm poll creation and choose a removal date
    else if (isset($_POST['fin_sondage_autre'])) {
        Utils::print_header(__('Step 3', 'Removal date and confirmation (3 on 3)'));
        bandeau_titre(__('Step 3', 'Removal date and confirmation (3 on 3)'));


        // Store choices in $_SESSION
        if (isset($_POST['choices'])) {
            $_SESSION['form']->clearChoices();
            foreach ($_POST['choices'] as $c) {
                if (!empty($c)) {
                    $c = strip_tags($c);
                    $choice = new Choice($c);
                    $_SESSION['form']->addChoice($choice);
                }
            }
        }

        // Expiration date is initialised with config parameter. Value will be modified in step 4 if user has defined an other date
        $_SESSION['form']->end_date = $max_expiry_time;

        // Summary
        $summary = '<ol>';
        foreach ($_SESSION['form']->getChoices() as $i=>$choice) {

            preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $choice->getName(), $md_a_img); // Markdown [![alt](src)](href)
            preg_match_all('/!\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_img); // Markdown ![alt](src)
            preg_match_all('/\[(.*?)\]\((.*?)\)/', $choice->getName(), $md_a); // Markdown [text](href)
            if (isset($md_a_img[2][0]) && $md_a_img[2][0] != '' && isset($md_a_img[3][0]) && $md_a_img[3][0] != '') { // [![alt](src)](href)

                $li_subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0] != '') ? stripslashes($md_a_img[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<a href="' . $md_a_img[3][0] . '"><img src="' . $md_a_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" /></a>';

            } elseif (isset($md_img[2][0]) && $md_img[2][0] != '') { // ![alt](src)

                $li_subject_text = (isset($md_img[1][0]) && $md_img[1][0] != '') ? stripslashes($md_img[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<img src="' . $md_img[2][0] . '" class="img-responsive" alt="' . $li_subject_text . '" />';

            } elseif (isset($md_a[2][0]) && $md_a[2][0] != '') { // [text](href)

                $li_subject_text = (isset($md_a[1][0]) && $md_a[1][0] != '') ? stripslashes($md_a[1][0]) : __('Generic', 'Choice') . ' ' . ($i + 1);
                $li_subject_html = '<a href="' . $md_a[2][0] . '">' . $li_subject_text . '</a>';

            } else { // text only

                $li_subject_text = stripslashes($choice->getName());
                $li_subject_html = $li_subject_text;

            }

            $summary .= '<li>' . $li_subject_html . '</li>' . "\n";
        }
        $summary .= '</ol>';

        $end_date_str = utf8_encode(strftime('%d/%m/%Y', $max_expiry_time)); //textual date

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'create_classic_poll.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="well summary">
                <h4>' . __('Step 3', 'List of your choices') . '</h4>
                ' . $summary . '
            </div>
            <div class="alert alert-info">
                <p>' . __('Step 3', 'Your poll will automatically be archived') . ' ' . $config['default_poll_duration'] . ' ' . __('Generic', 'days') . ' ' .__('Step 3', 'after the last date of your poll.') . '
                <br />' . __('Step 3', 'You can set a closer archiving date for it.') . '</p>
                <div class="form-group">
                    <label for="enddate" class="col-sm-5 control-label">' . __('Step 3', 'Archiving date:') . '</label>
                    <div class="col-sm-6">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="enddate" data-date-format="' . __('Date', 'dd/mm/yyyy') . '" aria-describedby="dateformat" name="enddate" value="' . $end_date_str . '" size="10" maxlength="10" placeholder="' . __('Date', 'dd/mm/yyyy') . '" />
                        </div>
                    </div>
                    <span id="dateformat" class="sr-only">' . __('Date', 'dd/mm/yyyy') . '</span>
                </div>
            </div>
            <div class="alert alert-warning">
                <p>' . __('Step 3', 'Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.') . '</p>';
        if ($config['use_smtp'] == true) {
            echo '
                <p>' . __('Step 3', 'Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.') . '</p>';
        }
        echo '
            </div>
            <p class="text-right">
                <button class="btn btn-default" onclick="javascript:window.history.back();" title="' . __('Step 3', 'Back to step 2') . '">' . __('Generic', 'Back') . '</button>
                <button name="confirmecreation" value="confirmecreation" type="submit" class="btn btn-success">' . __('Step 3', 'Create the poll') . '</button>
            </p>
        </div>
    </div>
    </form>' . "\n";

        bandeau_pied();

        // Step 2/4 : Select choices of the poll
    } else {
        Utils::print_header(__('Step 2 classic', 'Poll subjects (2 on 3)'));
        bandeau_titre(__('Step 2 classic', 'Poll subjects (2 on 3)'));

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'create_classic_poll.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">';
        echo '
            <div class="alert alert-info">
                <p>' . __('Step 2 classic', 'To make a generic poll you need to propose at least two choices between differents subjects.') . '</p>
                <p>' . __('Step 2 classic', 'You can add or remove additional choices with the buttons') . ' <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">' . __('Generic', 'Remove') . '</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">' . __('Generic', 'Add') . '</span></p>';
        if ($config['user_can_add_img_or_link']) {
            echo '    <p>' . __('Step 2 classic', 'It\'s possible to propose links or images by using') . ' <a href="http://' . $locale . '.wikipedia.org/wiki/Markdown">' . __('Step 2 classic', 'the Markdown syntax') . '</a>.</p>';
        }
        echo '    </div>' . "\n";

        // Fields choices : 5 by default
        $choices = $_SESSION['form']->getChoices();
        $nb_choices = max(count($choices), 5);
        for ($i = 0; $i < $nb_choices; $i++) {
            $choice = isset($choices[$i]) ? $choices[$i] : new Choice();
            echo '
            <div class="form-group choice-field">
                <label for="choice' . $i . '" class="col-sm-2 control-label">' . __('Generic', 'Choice') . ' ' . ($i + 1) . '</label>
                <div class="col-sm-10 input-group">
                    <input type="text" class="form-control" name="choices[]" size="40" value="' . $choice->getName() . '" id="choice' . $i . '" />';
            if ($config['user_can_add_img_or_link']) {
                echo '<span class="input-group-addon btn-link md-a-img" title="' . __('Step 2 classic', 'Add a link or an image') . ' - ' . __('Generic', 'Choice') . ' ' . ($i + 1) . '" ><span class="glyphicon glyphicon-picture"></span> <span class="glyphicon glyphicon-link"></span></span>';
            }
            echo '
            </div>
            </div>' . "\n";
        }

        echo '
            <div class="col-md-4">
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-choice" class="btn btn-default" title="' . __('Step 2 classic', 'Remove a choice') . '"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">' . __('Generic', 'Remove') . '</span></button>
                    <button type="button" id="add-a-choice" class="btn btn-default" title="' . __('Step 2 classic', 'Add a choice') . '"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">' . __('Generic', 'Add') . '</span></button>
                </div>
            </div>
            <div class="col-md-8 text-right">
                <a class="btn btn-default" href="' . Utils::get_server_name() . 'create_poll.php?type=classic" title="' . __('Step 2', 'Back to step 1') . '">' . __('Generic', 'Back') . '</a>
                <button name="fin_sondage_autre" value="' . __('Generic', 'Next') . '" type="submit" class="btn btn-success disabled" title="' . __('Step 2', 'Go to step 3') . '">' . __('Generic', 'Next') . '</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="md-a-imgModal" tabindex="-1" role="dialog" aria-labelledby="md-a-imgModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">' . __('Generic', 'Close') . '</span></button>
                    <p class="modal-title" id="md-a-imgModalLabel">' . __('Step 2 classic', 'Add a link or an image') . '</p>
                </div>
                <div class="modal-body">
                    <p class="alert alert-info">' . __('Step 2 classic', 'These fields are optional. You can add a link, an image or both.') . '</p>
                    <div class="form-group">
                        <label for="md-img"><span class="glyphicon glyphicon-picture"></span> ' . __('Step 2 classic', 'URL of the image') . '</label>
                        <input id="md-img" type="text" placeholder="http://…" class="form-control" size="40" />
                    </div>
                    <div class="form-group">
                        <label for="md-a"><span class="glyphicon glyphicon-link"></span> ' . __('Generic', 'Link') . '</label>
                        <input id="md-a" type="text" placeholder="http://…" class="form-control" size="40" />
                    </div>
                    <div class="form-group">
                        <label for="md-text">' . __('Step 2 classic', 'Alternative text') . '</label>
                        <input id="md-text" type="text" class="form-control" size="40" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">' . __('Generic', 'Cancel') . '</button>
                    <button type="button" class="btn btn-primary">' . __('Generic', 'Add') . '</button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <script type="text/javascript" src="js/app/framadatepicker.js"></script>
    <script type="text/javascript" src="js/app/classic_poll.js"></script>
    ' . "\n";

        bandeau_pied();

    }
}
