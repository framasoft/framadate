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
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
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
$editedVoteUniqueId = null;

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

    if ($poll->receiveNewVotes) {

        $subject = '[' . NOMAPPLICATION . '] ' . __f('Mail', 'Poll\'s participation: %s', $poll->title);

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
        $urlSondage = Utils::getUrlSondage($poll->admin_id, true);
        $message .= '<a href="' . $urlSondage . '">' . $urlSondage . '</a>' . "\n\n";

        $messageTypeKey = $type . '-' . $poll->id;
        $mailService->send($poll->admin_mail, $subject, $message, $messageTypeKey);
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
    $name = $inputService->filterName($_POST['name']);
    $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
    $slots_hash = $inputService->filterMD5($_POST['control']);

    if (empty($editedVote)) {
        $message = new Message('danger', __('Error', 'Something is going wrong...'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', __('Error', 'There is a problem with your choices'));
    }

    if ($message == null) {
        // Update vote
        try {
            $result = $pollService->updateVote($poll_id, $editedVote, $name, $choices, $slots_hash);
            if ($result) {
                if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                    $editedVoteUniqueId = filter_input(INPUT_POST, 'edited_vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
                    $urlEditVote = Utils::getUrlSondage($poll_id, false, $editedVoteUniqueId);
                    $message = new Message('success', __('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'), $urlEditVote);
                } else {
                    $message = new Message('success', __('studs', 'Update vote succeeded'));
                }
                sendUpdateNotification($poll, $mailService, $name, UPDATE_VOTE);
            } else {
                $message = new Message('danger', __('Error', 'Update vote failed'));
            }
        } catch (ConcurrentEditionException $cee) {
            $message = new Message('danger', __('Error', 'Poll has been updated before you vote'));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = $inputService->filterName($_POST['name']);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
    $slots_hash = $inputService->filterMD5($_POST['control']);

    if ($name == null) {
        $message = new Message('danger', __('Error', 'The name is invalid.'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', __('Error', 'There is a problem with your choices'));
    }

    if ($message == null) {
        // Add vote
        try {
            $result = $pollService->addVote($poll_id, $name, $choices, $slots_hash);
            if ($result) {
                if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                    $urlEditVote = Utils::getUrlSondage($poll_id, false, $result->uniqId);
                    $editedVoteUniqueId =  $result->uniqId;
                    $message = new Message('success', __('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'), $urlEditVote);
                } else {
                    $message = new Message('success', __('studs', 'Adding the vote succeeded'));
                }
                sendUpdateNotification($poll, $mailService, $name, ADD_VOTE);
            } else {
                $message = new Message('danger', __('Error', 'Adding vote failed'));
            }
        } catch (AlreadyExistsException $aee) {
            $message = new Message('danger', __('Error', 'You already voted'));
        } catch (ConcurrentEditionException $cee) {
            $message = new Message('danger', __('Error', 'Poll has been updated before you vote'));
        }
    }
}

// -------------------------------
// Add a comment
// -------------------------------

if (isset($_POST['add_comment'])) {
    $name = $inputService->filterName($_POST['name']);
    $comment = $inputService->filterComment($_POST['comment']);

    if ($name == null) {
        $message = new Message('danger', __('Error', 'The name is invalid.'));
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
$slots = $pollService->allSlotsByPoll($poll);
$votes = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', strtotime($poll->end_date) < time());
$smarty->assign('deletion_date', strtotime($poll->end_date) + PURGE_DELAY * 86400);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('slots_hash',  $pollService->hashSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('editedVoteUniqueId', $editedVoteUniqueId);

$smarty->display('studs.tpl');
