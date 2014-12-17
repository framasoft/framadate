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

/* Services */
/*----------*/

$pollService = new PollService($connect);
$inputService = new InputService();

/* PAGE */
/* ---- */

if(!empty($_GET['poll']) && strlen($_GET['poll']) === 24) {
    $admin_poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options'=>['regexp'=>'/^[a-z0-9]+$/']]);
    $poll_id = substr($admin_poll_id, 0, 16);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $smarty->assign('error', 'This poll doesn\'t exist');
    $smarty->display('error.tpl');
    exit;
}

// Retrieve data
$slots = $pollService->allSlotsByPollId($poll_id);
$votes = $pollService->allUserVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);


// Assign data to template
$smarty->assign('poll_id', $admin_poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', _('Poll') . ' - ' . $poll->title);
$smarty->assign('slots', $pollService->splitSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_moments', $pollService->computeBestMoments($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);

$smarty->display('studs.tpl');