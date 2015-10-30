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
use Framadate\Services\NotificationService;
use Framadate\Services\SecurityService;
use Framadate\Message;
use Framadate\Utils;
use Framadate\Editable;

include_once __DIR__ . '/app/inc/init.php';

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;
$message = null;
$editingVoteId = 0;
$accessGranted = true;
$resultPubliclyVisible = true;
$slots = array();
$votes = array();
$comments = array();

/* Services */
/*----------*/

$logService = new LogService();
$pollService = new PollService($connect, $logService);
$inputService = new InputService();
$mailService = new MailService($config['use_smtp']);
$notificationService = new NotificationService($mailService);
$securityService = new SecurityService();


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
// Password verification
// -------------------------------

if (!is_null($poll->password_hash)) {

    // If we came from password submission
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    if (!empty($password)) {
        $securityService->submitPollAccess($poll, $password);
    }

    if (!$securityService->canAccessPoll($poll)) {
        $accessGranted = false;
    }
    $resultPubliclyVisible = $poll->results_publicly_visible;

    if (!$accessGranted && !empty($password)) {
        $message = new Message('danger', __('Password', 'Wrong password'));
    } else if (!$accessGranted && !$resultPubliclyVisible) {
        $message = new Message('danger', __('Password', 'You have to provide a password to access the poll.'));
    } else if (!$accessGranted && $resultPubliclyVisible) {
        $message = new Message('danger', __('Password', 'You have to provide a password so you can participate to the poll.'));
    }
}

// We allow actions only if access is granted
if ($accessGranted) {

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
                $notificationService->sendUpdateNotification($poll, NotificationService::UPDATE_VOTE, $name);
            } else {
                $message = new Message('danger', __('Error', 'Update vote failed'));
            }
        }
    } elseif (isset($_POST['save'])) { // Add a new vote
        $name = $inputService->filterName($_POST['name']);
        $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);

        if ($name == null) {
            $message = new Message('danger', __('Error', 'The name is invalid.'));
        }
        if (count($choices) != count($_POST['choices'])) {
            $message = new Message('danger', __('Error', 'There is a problem with your choices'));
        }

        if ($message == null) {
            // Add vote
            $result = $pollService->addVote($poll_id, $name, $choices);
            if ($result) {
                if ($poll->editable == Editable::EDITABLE_BY_OWN) {
                    $urlEditVote = Utils::getUrlSondage($poll_id, false, $result->uniqId);
                    $message = new Message('success', __('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'), $urlEditVote);
                } else {
                    $message = new Message('success', __('studs', 'Adding the vote succeeded'));
                }
                $notificationService->sendUpdateNotification($poll, NotificationService::ADD_VOTE, $name);
            } else {
                $message = new Message('danger', __('Error', 'Adding vote failed'));
            }
        }
    }
}

// Retrieve data
if ($resultPubliclyVisible) {
    $slots = $pollService->allSlotsByPoll($poll);
    $votes = $pollService->allVotesByPollId($poll_id);
    $comments = $pollService->allCommentsByPollId($poll_id);
}

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', strtotime($poll->end_date) < time());
$smarty->assign('deletion_date', strtotime($poll->end_date) + PURGE_DELAY * 86400);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('accessGranted', $accessGranted);
$smarty->assign('resultPubliclyVisible', $resultPubliclyVisible);

$smarty->display('studs.tpl');
