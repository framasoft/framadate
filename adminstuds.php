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

use Doctrine\DBAL\DBALException;
use Framadate\Editable;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Exception\SlotAlreadyExistsException;
use Framadate\Message;
use Framadate\Security\PasswordHasher;
use Framadate\Services\NotificationService;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

/* Variables */
/* --------- */

$admin_poll_id = null;
$poll_id = null;
$poll = null;
$message = null;
$editingVoteId = 0;

/* Services */
/*----------*/

$adminPollService = Services::adminPoll();
$inputService = Services::input();
$notificationService = Services::notification();
$pollService = Services::poll();
$sessionService = Services::session();

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $admin_poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    if (strlen($admin_poll_id) === 24) {
        $poll = $pollService->findByAdminId($admin_poll_id);
    }
}

if ($poll) {
    $poll_id = $poll->id;
} else {
    $smarty->assign('error', __('Error', "This poll doesn't exist!"));
    $smarty->display('error.tpl');
    exit;
}

// -------------------------------
// creation message
// -------------------------------

$messagePollCreated = $sessionService->get("Framadate", "messagePollCreated", FALSE);

if ($messagePollCreated) {
	$sessionService->remove("Framadate", "messagePollCreated");

	$message = new Message('success', __('adminstuds', 'The poll was created.'));
}

// -------------------------------
// Update poll info
// -------------------------------

if (isset($_POST['update_poll_info'])) {
    $updated = false;
    $field = $inputService->filterAllowedValues($_POST['update_poll_info'], ['title', 'admin_mail', 'description',
        'rules', 'expiration_date', 'name', 'hidden', 'removePassword', 'password']);

    // Update the right poll field
    if ($field === 'title') {
        $title = $inputService->filterTitle($_POST['title']);
        if ($title) {
            $poll->title = $title;
            $updated = true;
        }
    } elseif ($field === 'admin_mail') {
        $admin_mail = $inputService->filterMail($_POST['admin_mail']);
        if ($admin_mail) {
            $poll->admin_mail = $admin_mail;
            $updated = true;
        }
    } elseif ($field === 'description') {
        $description = $inputService->filterDescription($_POST['description']);
        if ($description) {
            $poll->description = $description;
            $updated = true;
        }
    } elseif ($field === 'rules') {
        $rules = strip_tags($_POST['rules']);
        switch ($rules) {
            case 0:
                $poll->active = false;
                $poll->editable = Editable::NOT_EDITABLE;
                $updated = true;
                break;
            case 1:
                $poll->active = true;
                $poll->editable = Editable::NOT_EDITABLE;
                $updated = true;
                break;
            case 2:
                $poll->active = true;
                $poll->editable = Editable::EDITABLE_BY_ALL;
                $updated = true;
                break;
            case 3:
                $poll->active = true;
                $poll->editable = Editable::EDITABLE_BY_OWN;
                $updated = true;
                break;
        }
    } elseif ($field === 'expiration_date') {
        $expiration_date = $inputService->filterDate($_POST['expiration_date']);
        if ($expiration_date) {
            $poll->end_date = $expiration_date;
            $updated = true;
        }
    } elseif ($field === 'name') {
        $admin_name = $inputService->filterName($_POST['name']);
        if ($admin_name) {
            $poll->admin_name = $admin_name;
            $updated = true;
        }
    } elseif ($field === 'hidden') {
        $hidden = isset($_POST['hidden']) ? $inputService->filterBoolean($_POST['hidden']) : false;
        if ($hidden !== $poll->hidden) {
            $poll->hidden = $hidden;
	    $poll->results_publicly_visible = false;
            $updated = true;
        }
    } elseif ($field === 'removePassword') {
        $removePassword = isset($_POST['removePassword']) ? $inputService->filterBoolean($_POST['removePassword']) : false;
        if ($removePassword) {
            $poll->results_publicly_visible = false;
            $poll->password_hash = null;
            $updated = true;
        }
    } elseif ($field === 'password') {
        $password = isset($_POST['password']) ? $_POST['password'] : null;

        /**
         * Did the user choose results to be publicly visible ?
         */
        $resultsPubliclyVisible = isset($_POST['resultsPubliclyVisible']) ? $inputService->filterBoolean($_POST['resultsPubliclyVisible']) : false;
        /**
         * If there's one, save the password
         */
        if (!empty($password)) {
            $poll->password_hash =  PasswordHasher::hash($password);
            $updated = true;
        }

        /**
         * If not pasword was set and the poll should be hidden, hide the results
         */
        if ($poll->password_hash === null || $poll->hidden === true) {
            $poll->results_publicly_visible = false;
        }

        /**
         * We don't have a password, the poll is hidden and we change the results public visibility
         */
        if ($resultsPubliclyVisible !== $poll->results_publicly_visible && $poll->password_hash !== null && $poll->hidden === false) {
            $poll->results_publicly_visible = $resultsPubliclyVisible;
            $updated = true;
        }
    }

    // Update poll in database
    if ($updated && $adminPollService->updatePoll($poll)) {
        $message = new Message('success', __('adminstuds', 'Poll saved'));
        $notificationService->sendUpdateNotification($poll, NotificationService::UPDATE_POLL);
    } else {
        $message = new Message('danger', __('Error', 'Failed to save poll'));
        $poll = $pollService->findById($poll_id);
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

$selectedNewVotes = [];

if (!empty($_POST['save'])) { // Save edition of an old vote
    $name = $inputService->filterName($_POST['name']);
    if(empty($_POST['mail']) || $inputService->filterMail($_POST['mail'])===false) {
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
                $message = new Message('success', __('adminstuds', 'Vote updated'));
            } else {
                $message = new Message('danger', __('Error', 'Update vote failed'));
            }
        } catch (AlreadyExistsException $aee) {
            $message = new Message('danger', __('Error', "The name you've chosen already exists in this poll!"));
        } catch (ConcurrentEditionException $cee) {
            $message = new Message('danger', __('Error', 'Poll has been updated before you vote'));
        } catch (ConcurrentVoteException $cve) {
            $message = new Message('danger', __('Error', "Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = $inputService->filterName($_POST['name']);
    if(empty($_POST['mail']) || $inputService->filterMail($_POST['mail'])===false) {
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
                $message = new Message('success', __('adminstuds', 'Vote added'));
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

// -------------------------------
// Delete a votes
// -------------------------------

if (!empty($_GET['delete_vote'])) {
    $vote_id = filter_input(INPUT_GET, 'delete_vote', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BASE64_REGEX]]);
    $vote_id = Utils::base64url_decode($vote_id);
    if ($vote_id && $adminPollService->deleteVote($poll_id, $vote_id)) {
        $message = new Message('success', __('adminstuds', 'Vote deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete the vote!'));
    }
}

// -------------------------------
// Remove all votes
// -------------------------------

if (isset($_POST['remove_all_votes'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->display('confirm/delete_votes.tpl');
    exit;
}
if (isset($_POST['confirm_remove_all_votes'])) {
    if ($adminPollService->cleanVotes($poll_id)) {
        $message = new Message('success', __('adminstuds', 'All votes deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete all votes'));
    }
}

// -------------------------------
// Delete a comment
// -------------------------------

if (!empty($_POST['delete_comment'])) {
    $comment_id = filter_input(INPUT_POST, 'delete_comment', FILTER_VALIDATE_INT);

    if ($adminPollService->deleteComment($poll_id, $comment_id)) {
        $message = new Message('success', __('adminstuds', 'Comment deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete the comment'));
    }
}

// -------------------------------
// Remove all comments
// -------------------------------

if (isset($_POST['remove_all_comments'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->display('confirm/delete_comments.tpl');
    exit;
}
if (isset($_POST['confirm_remove_all_comments'])) {
    if ($adminPollService->cleanComments($poll_id)) {
        $message = new Message('success', __('adminstuds', 'All comments deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete all comments'));
    }
}

// -------------------------------
// Delete the entire poll
// -------------------------------

if (isset($_POST['delete_poll'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->display('confirm/delete_poll.tpl');
    exit;
}
if (isset($_POST['confirm_delete_poll'])) {
    if ($adminPollService->deleteEntirePoll($poll_id)) {
        $message = new Message('success', __('adminstuds', 'Poll fully deleted'));
        $notificationService->sendUpdateNotification($poll, NotificationService::DELETED_POLL);
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete the poll'));
    }
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->assign('message', $message);
    $smarty->display('poll_deleted.tpl');
    exit;
}

// -------------------------------
// Delete a slot
// -------------------------------

if (isset($_GET['delete_column'])) {
    $column = filter_input(INPUT_GET, 'delete_column', FILTER_DEFAULT);
    $column = Utils::base64url_decode($column);

    if ($poll->format === 'D') {
        $ex = explode('@', $column);

        $slot = new stdClass();
        $slot->title = $ex[0];
        $slot->moment = $ex[1];

        $result = $adminPollService->deleteDateSlot($poll, $slot);
    } else {
        $result = $adminPollService->deleteClassicSlot($poll, $column);
    }

    if ($result) {
        $message = new Message('success', __('adminstuds', 'Column deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete column'));
    }
}

// -------------------------------
// Collect the mails of a column
// -------------------------------

if (isset($_GET['collect_mail'])) {
    $column_str = strval(filter_input(INPUT_GET, 'collect_mail', FILTER_DEFAULT));
    $column_str = strval(Utils::base64url_decode($column_str));
    $column = intval($column_str);
    $votes = $pollService->splitVotes($pollService->allVotesByPollId($poll_id));
    $mails_yes = $mails_ifneedbe = $mails_no = [];
    foreach ($votes as $vote) {
        if (intval($vote->choices[$column]) === 2 && $vote->mail !== NULL) {
            $mails_yes[] = $vote->mail;
        } elseif (intval($vote->choices[$column]) === 1 && $vote->mail !== NULL) {
            $mails_ifneedbe[] = $vote->mail;
        } elseif($vote->mail !== NULL) {
            $mails_no[] = $vote->mail;
        }
    }

    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('admin', true);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title . ' - ' . __('adminstuds', 'Collect the emails of the polled users for the choice'));
    $smarty->assign('mails_yes', $mails_yes);
    $smarty->assign('mails_ifneedbe', $mails_ifneedbe);
    $smarty->assign('mails_no', $mails_no);
    $smarty->display('display_mails.tpl');
    exit;
}

// -------------------------------
// Add a slot
// -------------------------------

function exit_displaying_add_column($message = null) {
    global $smarty, $poll_id, $admin_poll_id, $poll;
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('format', $poll->format);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->assign('message', $message);
    $smarty->display('add_column.tpl');
    exit;
}

if (isset($_GET['add_column'])) {
    exit_displaying_add_column();
}

if (isset($_POST['confirm_add_column'])) {
    try {
        if (($poll->format === 'D' && empty($_POST['newdate']))
         || ($poll->format === 'A' && empty($_POST['choice']))) {
           exit_displaying_add_column(new Message('danger', __('Error', "Can't create an empty column.")));
        }
        if ($poll->format === 'D') {
            $date = $inputService->filterDate($_POST['newdate']);
            $time = $date->getTimestamp();
            $newmoment = strip_tags($_POST['newmoment']);
            $adminPollService->addDateSlot($poll_id, $time, $newmoment);
        } else {
            $newslot = strip_tags($_POST['choice']);
            $adminPollService->addClassicSlot($poll_id, $newslot);
        }

        $message = new Message('success', __('adminstuds', 'Choice added'));
    } catch (SlotAlreadyExistsException $e) {
        exit_displaying_add_column(new Message('danger', __f('Error', 'The column %s already exists', $e->getSlot())));
    } catch (MomentAlreadyExistsException $e) {
        exit_displaying_add_column(new Message('danger', __f('Error', 'The column %s already exists with %s', $e->getSlot(), $e->getMoment())));
    } catch (DBALException $e) {
        exit_displaying_add_column(new Message('danger', __('Error', 'Error while adding a column')));
    }
}

// Retrieve data
$slots = $pollService->allSlotsByPoll($poll);
$votes = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);
$deletion_date = clone $poll->end_date;
$deletion_date->add(new DateInterval('P' . PURGE_DELAY . 'D'));

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('admin_poll_id', $admin_poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', $poll->end_date < new DateTime());
$smarty->assign('deletion_date', $deletion_date);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('slots_hash', $pollService->hashSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes, $poll));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', true);
$smarty->assign('hidden', false);
$smarty->assign('accessGranted', true);
$smarty->assign('resultPubliclyVisible', true);
$smarty->assign('editedVoteUniqueId', '');
$smarty->assign('default_to_marldown_editor', $config['markdown_editor_by_default']);
$smarty->assign('selectedNewVotes', $selectedNewVotes);

$smarty->display('studs.tpl');
