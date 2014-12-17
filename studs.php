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
$poll_id = null;
$poll = null;
$message = null;

/* Services */
/*----------*/

$pollService = new PollService($connect);
$inputService = new InputService();

/* PAGE */
/* ---- */

if(!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[a-z0-9]+$/']]);
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
    // TODO Try what does filter_input with a wrong value
    $editingVoteId = filter_input(INPUT_POST, 'edit_vote', FILTER_VALIDATE_INT);
} else {
    $editingVoteId = 0;
}


// -------------------------------
// Something to save (edit or add)
// -------------------------------

if (!empty($_POST['save'])) { // Save edition of an old vote
    $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[012]$/']]);

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
        } else {
            $message = new Message('danger', _('Update vote failed.'));
        }
    }
} elseif (isset($_POST['save'])) { // Add a new vote
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[a-z0-9_ -]+$/i']]);
    $choices = $inputService->filterArray($_POST['choices'], FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[012]$/']]);

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
        } else {
            $message = new Message('danger', _('Update vote failed.'));
        }
    }
}

// -------------------------------
// Add a comment
// -------------------------------

if (isset($_POST['add_comment'])) {
    $name = filter_input(INPUT_POST, 'name', FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[a-z0-9_ -]+$/i']]);
    $comment = filter_input(INPUT_POST, 'comment', FILTER_DEFAULT);

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
$votes = $pollService->allUserVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', _('Poll') . ' - ' . $poll->title);
$smarty->assign('slots', $pollService->splitSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_moments', $pollService->computeBestMoments($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);

$smarty->display('studs.tpl');
