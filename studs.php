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
use Framadate\Editable;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Message;
use Framadate\Security\Token;
use Framadate\Services\NotificationService;
use Framadate\Services\SessionService;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

/* Constantes */
/* ---------- */

const USER_REMEMBER_VOTES_KEY = 'UserVotes';

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;
$message = null;
$editingVoteId = 0;
$accessGranted = true;
$resultPubliclyVisible = true;
$slots = [];
$votes = [];
$comments = [];
$selectedNewVotes = [];

/* Services */
/*----------*/

$inputService = Services::input();
$notificationService = Services::notification();
$pollService = Services::poll();
$securityService = Services::security();
$sessionService = Services::session();

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $smarty->assign('error', __('Error', 'This poll doesn\'t exist !'));
    $smarty->display('error.tpl');
    exit;
}

$editedVoteUniqueId = $sessionService->get(USER_REMEMBER_VOTES_KEY, $poll_id, '');

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
	if(empty($_POST['mail']) || $inputService->filterMail($_POST['mail']) === false) {
		$mail = null;
    	} else {
		$mail = $inputService->filterMail($_POST['mail']);
    	}
        $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
        $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
        $slots_hash = $inputService->filterMD5($_POST['control']);

        if (empty($editedVote)) {
            $message = new Message('danger', __('Error', 'Something has gone wrong...'));
        }
        if (count($choices) !== count($_POST['choices'])) {
            $message = new Message('danger', __('Error', 'There is a problem with your choices'));
        }

        if ($message === null) {
            // Update vote
            try {
                $result = $pollService->updateVote($poll_id, $editedVote, $name, $choices, $slots_hash, $mail);
                if ($result) {
                    if ($poll->editable === Editable::EDITABLE_BY_OWN) {
                        $editedVoteUniqueId = filter_input(INPUT_POST, 'edited_vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
                        $message = getMessageForOwnVoteEditableVote($sessionService, $smarty, $editedVoteUniqueId, $config['use_smtp'], $poll_id, $name);
                    } else {
                        $message = new Message('success', __('studs', 'Vote updated'));
                    }
                    $notificationService->sendUpdateNotification($poll, NotificationService::UPDATE_VOTE, $name);
                } else {
                    $message = new Message('danger', __('Error', 'Update vote failed'));
                }
            } catch (AlreadyExistsException $aee) {
	            $message = new Message('danger', __('Error', 'The name you\'ve chosen already exist in this poll!'));
	        } catch (ConcurrentEditionException $cee) {
                $message = new Message('danger', __('Error', 'Poll has been updated before you vote'));
            } catch (ConcurrentVoteException $cve) {
                $message = new Message('danger', __('Error', "Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
            }
        }
    } elseif (isset($_POST['save'])) { // Add a new vote
        $name = $inputService->filterName($_POST['name']);
	if(empty($_POST['mail']) || $inputService->filterMail($_POST['mail']) === false) {
		$mail = null;
    	} else {
		$mail = $inputService->filterMail($_POST['mail']);
    	}
        $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
        $slots_hash = $inputService->filterMD5($_POST['control']);

        if ($name === null) {
            $message = new Message('danger', __('Error', 'The name is invalid.'));
        }
        if (count($choices) !== count($_POST['choices'])) {
            $message = new Message('danger', __('Error', 'There is a problem with your choices'));
        }

        if ($message === null) {
            // Add vote
            try {
                $result = $pollService->addVote($poll_id, $name, $choices, $slots_hash, $mail);
                if ($result) {
                    if (intval($poll->editable) === Editable::EDITABLE_BY_OWN) {
                        $editedVoteUniqueId = $result->uniqId;
                        $message = getMessageForOwnVoteEditableVote($sessionService, $smarty, $editedVoteUniqueId, $config['use_smtp'], $poll_id, $name);
                    } else {
                        $message = new Message('success', __('studs', 'Vote added'));
                    }
                    $notificationService->sendUpdateNotification($poll, NotificationService::ADD_VOTE, $name);
                } else {
                    $message = new Message('danger', __('Error', 'Adding vote failed'));
                }
            } catch (AlreadyExistsException $aee) {
                $message = new Message('danger', __('Error', 'You already voted'));
                $selectedNewVotes = $choices;
            } catch (ConcurrentEditionException $cee) {
                $message = new Message('danger', __('Error', 'Poll has been updated before you vote'));
            } catch (ConcurrentVoteException $cve) {
                $message = new Message('danger', __('Error', "Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
            }
        }
    }
}

// Functions
function getMessageForOwnVoteEditableVote(SessionService &$sessionService, Smarty &$smarty, $editedVoteUniqueId, $canUseSMTP, $poll_id, $name) {
    $sessionService->set(USER_REMEMBER_VOTES_KEY, $poll_id, $editedVoteUniqueId);
    $urlEditVote = Utils::getUrlSondage($poll_id, false, $editedVoteUniqueId);
    $message = new Message(
        'success',
        __('studs', 'Your vote has been saved, but please note: you need to keep this personalised link to be able to edit your vote.'),
        $urlEditVote,
        __f('Poll results', 'Edit the line: %s', $name),
        'glyphicon-pencil');
    if ($canUseSMTP) {
        $token = new Token();
        $sessionService->set("Common", SESSION_EDIT_LINK_TOKEN, $token);
        $smarty->assign('editedVoteUniqueId', $editedVoteUniqueId);
        $smarty->assign('token', $token->getValue());
        $smarty->assign('poll_id', $poll_id);
        $message->includeTemplate = $smarty->fetch('part/form_remember_edit_link.tpl');
        $smarty->clearAssign('token');
    }
    return $message;
}

// Retrieve data
if ($resultPubliclyVisible || $accessGranted) {
    $slots = $pollService->allSlotsByPoll($poll);
    $votes = $pollService->allVotesByPollId($poll_id);
    $comments = $pollService->allCommentsByPollId($poll_id);
}

$deletion_date = clone $poll->end_date;
$deletion_date->add(new DateInterval('P' . PURGE_DELAY . 'D'));

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', $poll->end_date < new DateTime());
$smarty->assign('deletion_date', $deletion_date);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('slots_hash',  $pollService->hashSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes, $poll));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('accessGranted', $accessGranted);
$smarty->assign('resultPubliclyVisible', $resultPubliclyVisible);
$smarty->assign('editedVoteUniqueId', $editedVoteUniqueId);
$smarty->assign('ValueMax', $poll->ValueMax);
$smarty->assign('selectedNewVotes', $selectedNewVotes);

$smarty->display('studs.tpl');
