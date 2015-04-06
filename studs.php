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

        $subject = '[' . NOMAPPLICATION . '] ' . _('Poll\'s participation') . ' : ' . $poll->title;

        $message = $name . ' ';
        switch ($type) {
            case UPDATE_VOTE:
                $message .= _('updated a vote.\nYou can find your poll at the link') . " :\n\n";
                break;
            case ADD_VOTE:
                $message .= _('filled a vote.\nYou can find your poll at the link') . " :\n\n";
                break;
            case ADD_COMMENT:
                $message .= _('wrote a comment.\nYou can find your poll at the link') . " :\n\n";
                break;
        }
        $message .= Utils::getUrlSondage($poll->admin_id, true) . "\n\n";
        $message .= _('Thanks for your confidence.') . "\n" . NOMAPPLICATION;

        $mailService->send($poll->admin_mail, $subject, $message);

        $_SESSION['mail_sent'][$poll->id] = true;
    }
}

/* PAGE */
/* ---- */

if (!empty($_POST['poll']) || !empty($_GET['poll'])) {
    if (!empty($_POST['poll']))
        $inputType = INPUT_POST;
    else
        $inputType = INPUT_GET;
    $poll_id = filter_input($inputType, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $smarty->assign('error', _('This poll doesn\'t exist !'));
    $smarty->display('error.tpl');
    exit;
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
        $message = new Message('danger', _('Something is going wrong...'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', _('There is a problem with your choices.'));
    }

    if ($message == null) {
        // Update vote
        $result = $pollService->updateVote($poll_id, $editedVote, $name, $choices);
        if ($result) {
            $message = new Message('success', _('Update vote successfully.'));
            sendUpdateNotification($poll, $mailService, $name, UPDATE_VOTE);
        } else {
            $message = new Message('danger', _('Update vote failed.'));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);

    if (empty($name)) {
        $message = new Message('danger', _('Name is incorrect.'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', _('There is a problem with your choices.'));
    }

    if ($message == null) {
        // Add vote
        $result = $pollService->addVote($poll_id, $name, $choices);
        if ($result) {
            if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                $urlEditVote = Utils::getUrlSondage($poll_id, false, $result->uniqId);
                $message = new Message('success', __('studs', "Your vote has been registered successfully, but be careful : regarding this poll options, you need to keep this personal link to edit your own vote : "), $urlEditVote);
            } else {
                $message = new Message('success', _('Update vote successfully.'));
            }
            sendUpdateNotification($poll, $mailService, $name, ADD_VOTE);
        } else {
            $message = new Message('danger', _('Update vote failed.'));
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
        $message = new Message('danger', _('Name is incorrect.'));
    }

    if ($message == null) {
        // Add comment
        $result = $pollService->addComment($poll_id, $name, $comment);
        if ($result) {
            $message = new Message('success', _('Comment added.'));
            sendUpdateNotification($poll, $mailService, $name, ADD_COMMENT);
        } else {
            $message = new Message('danger', _('Comment failed.'));
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
$smarty->assign('title', _('Poll') . ' - ' . $poll->title);
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
$smarty->assign('parameter_name_regex', NAME_REGEX);

$smarty->display('studs.tpl');
