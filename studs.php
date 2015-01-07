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

include_once __DIR__ . '/app/inc/init.php';

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
 * @param $poll Object The poll
 * @param $mailService MailService The mail service
 */
function sendUpdateNotification($poll, $mailService) {
    if ($poll->receiveNewVotes && !isset($_SESSION['mail_sent'][$poll->id])) {

        $subject = '[' . NOMAPPLICATION . '] ' . _('Poll\'s participation') . ' : ' . $poll->title;
        $message = html_entity_decode('"$nom" ', ENT_QUOTES, 'UTF-8') .
            _('has filled a line.\nYou can find your poll at the link') . " :\n\n" .
            Utils::getUrlSondage($poll->admin_poll_id, true) . " \n\n" .
            _('Thanks for your confidence.') . "\n" . NOMAPPLICATION;

        $mailService->send($poll->admin_mail, $subject, $message);

        $_SESSION["mail_sent"][$poll->id] = true;
    }
}

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $smarty->assign('error', 'This poll doesn\'t exist');
    $smarty->display('error.tpl');
    exit;
}

// -------------------------------
// A vote is going to be edited
// -------------------------------

if (!empty($_POST['edit_vote'])) {
    $editingVoteId = filter_input(INPUT_POST, 'edit_vote', FILTER_VALIDATE_INT);
}


// -------------------------------
// Something to save (edit or add)
// -------------------------------

if (!empty($_POST['save'])) { // Save edition of an old vote
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
        $result = $pollService->updateVote($poll_id, $editedVote, $choices);
        if ($result) {
            $message = new Message('success', _('Update vote successfully.'));
            sendUpdateNotification($poll, $mailService);
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
            $message = new Message('success', _('Update vote successfully.'));
            sendUpdateNotification($poll, $mailService);
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
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);

$smarty->display('studs.tpl');
