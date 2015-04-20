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
use Framadate\Services\InputService;
use Framadate\Services\MailService;
use Framadate\Services\RestrictedAccessService;
use Framadate\Message;
use Framadate\Utils;
use Framadate\Editable;

include_once __DIR__ . '/app/inc/init.php';

/* Constants */
/* --------- */
const UPDATE_VOTE = 1;
const ADD_VOTE = 2;
const ADD_COMMENT = 3;

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;
$message = null;
$editingVoteId = 0;

/* Services */
/*----------*/

$logService = new LogService();
$pollService = new PollService($connect, $logService);
$inputService = new InputService();
$mailService = new MailService($config['use_smtp']);
$restrictedAccessService = new RestrictedAccessService();

/* Functions */
/*-----------*/

/**
 * Send a notification to the poll admin to notify him about an update.
 *
 * @param $poll stdClass The poll
 * @param $mailService MailService The mail service
 * @param $name string The name user who triggered the notification
 * @param $type int cf: Constants on the top of this page
 */
function sendUpdateNotification($poll, $mailService, $name, $type) {
    if (!isset($_SESSION['mail_sent'])) {
        $_SESSION['mail_sent'] = [];
    }

    if ($poll->receiveNewVotes && (!isset($_SESSION['mail_sent'][$poll->id]) || $_SESSION['mail_sent'][$poll->id] !== true)) {

        $subject = '[' . NOMAPPLICATION . '] ' . __('Mail', 'Poll\'s participation') . ' : ' . $poll->title;

        $message = $name . ' ';
        switch ($type) {
            case UPDATE_VOTE:
                $message .= __('Mail', "updated a vote.\nYou can find your poll at the link") . " :\n\n";
                break;
            case ADD_VOTE:
                $message .= __('Mail', "filled a vote.\nYou can find your poll at the link") . " :\n\n";
                break;
            case ADD_COMMENT:
                $message .= __('Mail', "wrote a comment.\nYou can find your poll at the link") . " :\n\n";
                break;
        }
        $message .= Utils::getUrlSondage($poll->admin_id, true) . "\n\n";
        $message .= __('Mail', 'Thanks for your confidence.') . "\n" . NOMAPPLICATION;

        $mailService->send($poll->admin_mail, $subject, $message);

        $_SESSION['mail_sent'][$poll->id] = true;
    }
}

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    if (strlen($poll_id) === 16) {
        $poll = $pollService->findById($poll_id);
    }
}

if (!$poll) {
    $smarty->assign('error', __('Error', 'This poll doesn\'t exist !'));
    $smarty->display('error.tpl');
    exit;
}

/* RESTRICTED ACCESS */
/* ----------------- */

$is_restricted = false;
$has_access = true;
if ($poll->password_hash != null) {
    $is_restricted = true;
    $login_message = null;

    if (isset($_POST['password'])) {
        if ($restrictedAccessService->compareAccess($poll, $_POST['password'])) {
            $login_message = new Message("success", __("Restricted poll", "The poll access is authorised."));
        } else {
            $login_message = new Message("danger", __("Restricted poll", "Wrong password."));
        }
    }

    $has_access = $restrictedAccessService->hasAccess($poll->id);
    $smarty->assign("login_message", $login_message);


    if (!$poll->results_publicly_visible && !$has_access) {
        $smarty->assign("poll", $poll);
        $smarty->assign("has_access", $has_access);
        $smarty->display("poll_access_page.tpl");
        exit;
    }
}



// -------------------------------
// A vote is going to be edited
// -------------------------------

if (!empty($_GET['vote'])) {
    $editingVoteId = filter_input(INPUT_GET, 'vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
}

// -------------------------------
// Something to save (edit or add)
// -------------------------------

if (!empty($_POST['save'])) { // Save edition of an old vote
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);

    if (empty($editedVote)) {
        $message = new Message('danger', __('Error', 'Something is going wrong...'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', __('Error', 'There is a problem with your choices'));
    }

    if ($message == null) {
        // Update vote
        $result = $pollService->updateVote($poll_id, $editedVote, $name, $choices);
        if ($result) {
            if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                $editedVoteUniqId = filter_input(INPUT_POST, 'edited_vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
                $urlEditVote = Utils::getUrlSondage($poll_id, false, $editedVoteUniqId);
                $message = new Message('success', __('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'), $urlEditVote);
            } else {
                $message = new Message('success', __('studs', 'Update vote succeeded'));
            }
            sendUpdateNotification($poll, $mailService, $name, UPDATE_VOTE);
        } else {
            $message = new Message('danger', __('Error', 'Update vote failed'));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);

    if (empty($name)) {
        $message = new Message('danger', __('Error', 'Name is incorrect'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', __('There is a problem with your choices'));
    }

    if ($message == null) {
        // Add vote
        $result = $pollService->addVote($poll_id, $name, $choices);
        if ($result) {
            if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                $urlEditVote = Utils::getUrlSondage($poll_id, false, $result->uniqId);
                $message = new Message('success', __('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'), $urlEditVote);
            } else {
                $message = new Message('success', __('studs', 'Update vote succeeded'));
            }
            sendUpdateNotification($poll, $mailService, $name, ADD_VOTE);
        } else {
            $message = new Message('danger', __('Error', 'Update vote failed'));
        }
    }
}

// -------------------------------
// Add a comment
// -------------------------------

if (isset($_POST['add_comment'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $comment = strip_tags($_POST['comment']);

    if (empty($name)) {
        $message = new Message('danger', __('Error', 'Name is incorrect'));
    }

    if ($message == null) {
        // Add comment
        $result = $pollService->addComment($poll_id, $name, $comment);
        if ($result) {
            $message = new Message('success', __('Comments', 'Comment added'));
            sendUpdateNotification($poll, $mailService, $name, ADD_COMMENT);
        } else {
            $message = new Message('danger', __('Error', 'Comment failed'));
        }
    }

}

// Retrieve data
$slots = $pollService->allSlotsByPollId($poll_id);
$votes = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', strtotime($poll->end_date) < time());
$smarty->assign('deletion_date', $poll->end_date + PURGE_DELAY * 86400);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('is_restricted', $is_restricted);
$smarty->assign('has_access', $has_access);
$smarty->assign('parameter_name_regex', NAME_REGEX);
$smarty->assign('login_message_danger', new Message("danger", "There is an error"));
$smarty->assign('login_message_ok', new Message("success", "There is no problem !"));

$smarty->display('studs.tpl');
