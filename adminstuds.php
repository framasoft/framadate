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
use Framadate\Services\InputService;
use Framadate\Message;
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

$pollService = new PollService($connect);
$inputService = new InputService();

/* PAGE */
/* ---- */

if (!empty($_GET['poll']) && strlen($_GET['poll']) === 24) {
    $admin_poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-z0-9]+$/']]);
    $poll_id = substr($admin_poll_id, 0, 16);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $smarty->assign('error', 'This poll doesn\'t exist');
    $smarty->display('error.tpl');
    exit;
}

// -------------------------------
// Update poll info
// -------------------------------

if (isset($_POST['update_poll_info'])) {
    $updated = false;
    $field = $inputService->filterAllowedValues($_POST['update_poll_info'], ['title', 'admin_mail', 'comment', 'rules']);

    // Update the right poll field
    if ($field == 'title') {
        $title = $filter_input(INPUT_POST, 'title', FILTER_DEFAULT);
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
    } elseif ($field == 'comment') {
        $comment = filter_input(INPUT_POST, 'comment', FILTER_DEFAULT);
        if ($comment) {
            $poll->comment = $comment;
            $updated = true;
        }
    } elseif ($field == 'rules') {
        $rules = filter_input(INPUT_POST, 'rules', FILTER_DEFAULT);
        switch ($rules) {
            case 0:
                $poll->active = false;
                $poll->editable = false;
                $updated = true;
                break;
            case 1:
                $poll->active = true;
                $poll->editable = false;
                $updated = true;
                break;
            case 2:
                $poll->active = true;
                $poll->editable = true;
                $updated = true;
                break;
        }
    }

    // Update poll in database
    if ($updated && $pollService->updatePoll($poll)) {
        $message = new Message('success', _('Poll saved.'));
    } else {
        $message = new Message('danger', _('Failed to save poll.'));
    }
}

// -------------------------------
// Delete a comment
// -------------------------------

if (!empty($_POST['delete_comment'])) {
    $comment_id = filter_input(INPUT_POST, 'delete_comment', FILTER_VALIDATE_INT);

    if ($pollService->deleteComment($poll_id, $comment_id)) {
        $message = new Message('success', _('Comment deleted.'));
    } else {
        $message = new Message('danger', _('Failed to delete the comment.'));
    }
}

// -------------------------------
// Remove all votes
// -------------------------------
if (isset($_POST['remove_all_votes'])) {
    $pollService->cleanVotes($poll_id);
}

// -------------------------------
// Remove all comments
// -------------------------------
if (isset($_POST['remove_all_comments'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', _('Poll') . ' - ' . $poll->title);
    $smarty->display('confirm/delete_comment.tpl');
    exit;
}
if (isset($_POST['confirm_remove_all_comments'])) {
    if ($pollService->cleanComments($poll_id)) {
        $message = new Message('success', _('All comments deleted.'));
    } else {
        $message = new Message('danger', _('Failed to delete all comments.'));
    }
}

// -------------------------------
// Delete the entire poll
// -------------------------------

if (isset($_POST['delete_poll'])) {
    $smarty->assign('poll_id', $poll_id);
    $smarty->assign('admin_poll_id', $admin_poll_id);
    $smarty->assign('title', _('Poll') . ' - ' . $poll->title);
    $smarty->display('confirm/delete_poll.tpl');
    exit;
}
if (isset($_POST['confirm_delete_poll'])) {
    // TODO
}

// Retrieve data
$slots = $pollService->allSlotsByPollId($poll_id);
$votes = $pollService->allUserVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('admin_poll_id', $admin_poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', _('Poll') . ' - ' . $poll->title);
$smarty->assign('slots', $pollService->splitSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_moments', $pollService->computeBestMoments($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', true);

$smarty->display('studs.tpl');