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
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

/* Functions */
/* --------- */

function split_slots($slots) {
    $splitted = array();
    foreach ($slots as $slot) {
        $ex = explode('@', $slot->sujet);
        $obj = new \stdClass();
        $obj->day = $ex[0];
        $obj->moments = explode(',', $ex[1]);

        $splitted[] = $obj;
    }
    return $splitted;
}

function split_votes($votes) {
    $splitted = array();
    foreach ($votes as $vote) {
        $obj = new \stdClass();
        $obj->id = $vote->id_users;
        $obj->name = $vote->nom;
        $obj->choices = str_split($vote->reponses);

        $splitted[] = $obj;
    }
    return $splitted;
}

function computeBestMoments($votes) {
    $result = [];
    foreach ($votes as $vote) {
        $choices = str_split($vote->reponses);
        foreach ($choices as $i=>$choice) {
            if (empty($result[$i])) {
                $result[$i] = 0;
            }
            if ($choice == 2) {
                $result[$i]++;
            }
        }
    }
    return $result;
}

/* Services */
/*----------*/

$pollService = new PollService($connect);

/* PAGE */
/* ---- */

if(!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[a-z0-9]+$/']]);
}


$poll = $pollService->findById($poll_id);

if (!$poll) {
    $smarty->assign('error', 'This poll doesn\'t exist');
    $smarty->display('error.tpl');
    exit;
}

// A vote is going to be edited
if (!empty($_POST['edit_vote'])) {
    // TODO Try what does filter_input with a wrong value
    $editingVoteId = filter_input(INPUT_POST, 'edit_vote', FILTER_VALIDATE_INT);
} else {
    $editingVoteId = 0;
}


if (!empty($_POST['save'])) { // Save edition of an old vote
    $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
    $newChoices = [];

    // TODO Do this verification into a Service (maybe called 'InputService')
    foreach($_POST['choices'] as $id=>$choice) {
        $choice = filter_var($choice, FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[012]$/']]);
        if ($choice !== false) {
            $newChoices[$id] = $choice;
        }
    }

    if (count($newChoices) == count($_POST['choices'])) {
        $result = $pollService->updatePoll($poll_id, $editedVote, $newChoices);
        if ($result) {
            $message = ['type'=>'success', 'message'=>_('Update vote successfully!')];
        } else {
            $message = ['type'=>'success', 'message'=>_('Update vote successfully!')];
        }
    }
} elseif (isset($_POST[''])) { // Add a new vote
}

// Retrieve data
$slots = $pollService->allSlotsByPollId($poll_id);
$votes = $pollService->allUserVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', _('Poll') . ' - ' . $poll->title);
$smarty->assign('slots', split_slots($slots));
$smarty->assign('votes', split_votes($votes));
$smarty->assign('best_moments', computeBestMoments($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);

//Utils::debug(computeBestMoments($votes));exit;

$smarty->display('studs.tpl');
