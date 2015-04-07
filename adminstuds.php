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
use Framadate\Services\PollService;
use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Message;
use Framadate\Utils;
use Framadate\Editable;

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

$logService = new LogService();
$pollService = new PollService($connect, $logService);
$adminPollService = new AdminPollService($connect, $pollService, $logService);
$inputService = new InputService();

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $admin_poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    if (strlen($admin_poll_id) === 24) {
        $poll_id = substr($admin_poll_id, 0, 16);
        $poll = $pollService->findById($poll_id);
    }
}

if (!$poll) {
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
        $title = strip_tags($_POST['title']);
        if ($title) {
            $poll->title = $title;
            $updated = true;
        }
    } elseif ($field == 'admin_mail') {
        $admin_mail = filter_input(INPUT_POST, 'admin_mail', FILTER_VALIDATE_EMAIL);
        if ($admin_mail) {
            $poll->admin_mail = $admin_mail;
            $updated = true;
        }
    } elseif ($field == 'description') {
        $description = strip_tags($_POST['description']);
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
        $expiration_date = filter_input(INPUT_POST, 'expiration_date', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]+[-/][0-9]+[-/][0-9]+#']]);
        $expiration_date = strtotime($expiration_date);
        if ($expiration_date) {
            $poll->end_date = $expiration_date;
            $updated = true;
        }
    } elseif ($field == 'name') {
        $admin_name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
        if ($admin_name) {
            $poll->admin_name = $admin_name;
            $updated = true;
        }
    } elseif ($field == 'hidden') {
        $hidden = filter_input(INPUT_POST, 'hidden', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_REGEX]]);
        $hidden = $hidden==null?false:true;
        if ($hidden != $poll->hidden) {
            $poll->hidden = $hidden;
            $updated = true;
        }
    }

    // Update poll in database
    if ($updated && $adminPollService->updatePoll($poll)) {
        $message = new Message('success', __('adminstuds', 'Poll saved'));
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
            $message = new Message('success', __('adminstuds', 'Vote updated'));
        } else {
            $message = new Message('danger', __('Error', 'Update vote failed'));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);

    if (empty($name)) {
        $message = new Message('danger', __('Error', 'The name is invalid'));
    }
    if (count($choices) != count($_POST['choices'])) {
        $message = new Message('danger', __('Error', 'There is a problem with your choices'));
    }

    if ($message == null) {
        // Add vote
        $result = $pollService->addVote($poll_id, $name, $choices);
        if ($result) {
            $message = new Message('success', __('adminstuds', 'Vote added'));
        } else {
            $message = new Message('danger', __('Error', 'Adding vote failed'));
        }
    }
}

// -------------------------------
// Delete a votes
// -------------------------------

if (!empty($_POST['delete_vote'])) {
    $vote_id = filter_input(INPUT_POST, 'delete_vote', FILTER_VALIDATE_INT);
    if ($adminPollService->deleteVote($poll_id, $vote_id)) {
        $message = new Message('success', __('adminstuds', 'Vote deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete the vote'));
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
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => NAME_REGEX]]);
    $comment = strip_tags($_POST['comment']);

    if (empty($name)) {
        $message = new Message('danger', __('Error', 'The name is invalid'));
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

if (!empty($_POST['delete_column'])) {
    $column = filter_input(INPUT_POST, 'delete_column', FILTER_DEFAULT);

    if ($poll->format === 'D') {
        $ex = explode('@', $column);

        $slot = new stdClass();
        $slot->title = $ex[0];
        $slot->moment = $ex[1];

        $result = $adminPollService->deleteDateSlot($poll_id, $slot);
    } else {
        $result = $adminPollService->deleteClassicSlot($poll_id, $column);
    }

    if ($result) {
        $message = new Message('success', __('adminstuds', 'Column deleted'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to delete the column'));
    }
}

// -------------------------------
// Add a slot
// -------------------------------

if (isset($_POST['add_slot'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('format', $poll->format);
    $smarty->assign('title', __('Generic', 'Poll') . ' - ' . $poll->title);
    $smarty->display('add_slot.tpl');
    exit;
}
if (isset($_POST['confirm_add_slot'])) {
    if ($poll->format === 'D') {
        $newdate = strip_tags($_POST['newdate']);
        $newmoment = strip_tags($_POST['newmoment']);

        $ex = explode('/', $newdate);
        $result = $adminPollService->addSlot($poll_id, mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]), $newmoment);
    } else {
        $newslot = strip_tags($_POST['choice']);
        $result = $adminPollService->addSlot($poll_id, $newslot, null);
    }

    if ($result) {
        $message = new Message('success', __('adminstuds', 'Choice added'));
    } else {
        $message = new Message('danger', __('Error', 'Failed to add the column'));
    }
}

// Retrieve data
$slots = $pollService->allSlotsByPollId($poll_id);
$votes = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('admin_poll_id', $admin_poll_id);
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
$smarty->assign('admin', true);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('parameter_name_regex', NAME_REGEX);

$smarty->display('studs.tpl');