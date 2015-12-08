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
use Framadate\Editable;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Message;
use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

/* Constants */
/* --------- */
const UPDATE_POLL = 1;
const DELETED_POLL = 2;

/* Variables */
/* --------- */

$admin_poll_id = null;
$poll_id = null;
$poll = null;
$message = null;
$editingVoteId = 0;

/* Services */
/*----------*/

$logService = new LogService();
$pollService = new PollService($connect, $logService);
$adminPollService = new AdminPollService($connect, $pollService, $logService);
$inputService = new InputService();
$mailService = new MailService($config['use_smtp']);

/* Functions */
/*-----------*/

/**
 * Send a notification to the poll admin to notify him about an update.
 *
 * @param stdClass $poll The poll
 * @param MailService $mailService The mail service
 * @param int $type cf: Constants on the top of this page
 */
function sendUpdateNotification($poll, $mailService, $type) {
    if (!isset($_SESSION['mail_sent'])) {
        $_SESSION['mail_sent'] = [];
    }

    if ($poll->receiveNewVotes) {

        $subject = '[' . NOMAPPLICATION . '] ' . __f('Mail', 'Notification of poll: %s', $poll->title);

        $message = '';
        switch ($type) {
            case UPDATE_POLL:
                $message = __f('Mail', 'Someone just change your poll available at the following link %s.', Utils::getUrlSondage($poll->admin_id, true)) . "\n\n";
                break;
            case DELETED_POLL:
                $message = __f('Mail', 'Someone just delete your poll %s.', Utils::htmlEscape($poll->title)) . "\n\n";
                break;
        }

        $messageTypeKey = $type . '-' . $poll->id;
        $mailService->send($poll->admin_mail, $subject, $message, $messageTypeKey);
    }
}

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
    $smarty->assign('error', __('Error', 'This poll doesn\'t exist !'));
    $smarty->display('error.tpl');
    exit;
}

// -------------------------------
// Update poll info
// -------------------------------

if (isset($_POST['update_poll_info'])) {
    $updated = false;
    $field = $inputService->filterAllowedValues($_POST['update_poll_info'], ['title', 'admin_mail', 'description', 'rules', 'expiration_date', 'name', 'hidden']);

    // Update the right poll field
    if ($field == 'title') {
        $title = $inputService->filterTitle($_POST['title']);
        if ($title) {
            $poll->title = $title;
            $updated = true;
        }
    } elseif ($field == 'admin_mail') {
        $admin_mail = $inputService->filterMail($_POST['admin_mail']);
        if ($admin_mail) {
            $poll->admin_mail = $admin_mail;
            $updated = true;
        }
    } elseif ($field == 'description') {
        $description = $inputService->filterDescription($_POST['description']);
        if ($description) {
            $poll->description = $description;
            $updated = true;
        }
    } elseif ($field == 'rules') {
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
    } elseif ($field == 'expiration_date') {
        $expiration_date = filter_input(INPUT_POST, 'expiration_date', FILTER_VALIDATE_REGEXP,
            ['options' => ['regexp' => '#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#']]);
        if ($expiration_date) {
            $poll->end_date = $expiration_date;
            $updated = true;
        }
    } elseif ($field == 'name') {
        $admin_name = $inputService->filterName($_POST['name']);
        if ($admin_name) {
            $poll->admin_name = $admin_name;
            $updated = true;
        }
    } elseif ($field == 'hidden') {
        $hidden = isset($_POST['hidden']) ? $inputService->filterBoolean($_POST['hidden']) : false;
        if ($hidden != $poll->hidden) {
            $poll->hidden = $hidden;
            $updated = true;
        }
    }

    // Update poll in database
    if ($updated && $adminPollService->updatePoll($poll)) {
        $message = new Message('success', __('adminstuds', 'Poll saved'));
        sendUpdateNotification($poll, $mailService, UPDATE_POLL);
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
                $message = new Message('success', __('adminstuds', 'Vote updated'));
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
                $message = new Message('success', __('adminstuds', 'Vote added'));
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
        } else {
            $message = new Message('danger', __('Error', 'Comment failed'));
        }
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
        sendUpdateNotification($poll, $mailService, DELETED_POLL);
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

if (!empty($_GET['delete_column'])) {
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
        $message = new Message('success', __('adminstuds', 'Column removed'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete column'));
    }
}

// -------------------------------
// Add a slot
// -------------------------------

if (isset($_GET['add_column'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('format', $poll->format);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->display('add_column.tpl');
    exit;
}
if (isset($_POST['confirm_add_column'])) {
    try {
        if ($poll->format === 'D') {
            $newdate = strip_tags($_POST['newdate']);
            $newmoment = str_replace(',', '-', strip_tags($_POST['newmoment']));

            $ex = explode('/', $newdate);
            $adminPollService->addDateSlot($poll_id, mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]), $newmoment);
        } else {
            $newslot = str_replace(',', '-', strip_tags($_POST['choice']));
            $adminPollService->addClassicSlot($poll_id, $newslot);
        }

        $message = new Message('success', __('adminstuds', 'Choice added'));
    } catch (MomentAlreadyExistsException $e) {
        $message = new Message('danger', __('Error', 'The column already exists'));
    }
}

// Retrieve data
$slots = $pollService->allSlotsByPoll($poll);
$votes = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('admin_poll_id', $admin_poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
$smarty->assign('expired', strtotime($poll->end_date) < time());
$smarty->assign('deletion_date', strtotime($poll->end_date) + PURGE_DELAY * 86400);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('slots_hash', $pollService->hashSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', true);
$smarty->assign('hidden', false);

$smarty->display('studs.tpl');
