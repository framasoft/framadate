<?php

/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt.
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

include_once __DIR__.'/app/inc/init.php';

/* Constants */
/* --------- */
const UPDATE_VOTE = 1;
const ADD_VOTE    = 2;
const ADD_COMMENT = 3;

/* Variables */
/* --------- */

$poll_id            = null;
$poll               = null;
$message            = null;
$editingVoteId      = 0;
$editedVoteUniqueId = null;

/* Services */
/*----------*/

$logService   = new LogService();
$pollService  = new PollService($connect, $logService);
$inputService = new InputService();
$mailService  = new MailService($config['use_smtp']);

/* Functions */
/*-----------*/

/**
 * Send a notification to the poll admin to notify him about an update.
 *
 * @param $poll stdClass The poll
 * @param $mailService MailService The mail service
 * @param $name string The name user who triggered the notification
 * @param $type int cf: Constants on the top of this page
 */
function sendUpdateNotification($poll, $mailService, $name, $type)
{
    if (!isset($_SESSION['mail_sent'])) {
        $_SESSION['mail_sent'] = [];
    }

    if ($poll->receiveNewVotes) {
        $subject = '['.NOMAPPLICATION.'] '.__f('Mail', 'Poll\'s participation: %s', $poll->title);

        $message = $name.' ';
        switch ($type) {
            case UPDATE_VOTE:
                $message .= __('Mail', "updated a vote.\nYou can find your poll at the link")." :\n\n";
                break;
            case ADD_VOTE:
                $message .= __('Mail', "filled a vote.\nYou can find your poll at the link")." :\n\n";
                break;
            case ADD_COMMENT:
                $message .= __('Mail', "wrote a comment.\nYou can find your poll at the link")." :\n\n";
                break;
        }
        $urlSondage = Utils::getUrlSondage($poll->admin_id, true);
        $message .= '<a href="'.$urlSondage.'">'.$urlSondage.'</a>'."\n\n";

        $messageTypeKey = $type.'-'.$poll->id;
        $mailService->send($poll->admin_mail, $subject, $message, $messageTypeKey);
    }
}

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

// Retrieve data
$slots    = $pollService->allSlotsByPoll($poll);
$votes    = $pollService->allVotesByPollId($poll_id);
$comments = $pollService->allCommentsByPollId($poll_id);

// Assign data to template
$smarty->assign('poll_id', $poll_id);
$smarty->assign('poll', $poll);
$smarty->assign('title', __('Generic', 'Poll').' - '.$poll->title);
$smarty->assign('expired', strtotime($poll->end_date) < time());
$smarty->assign('deletion_date', strtotime($poll->end_date) + PURGE_DELAY * 86400);
$smarty->assign('slots', $poll->format === 'D' ? $pollService->splitSlots($slots) : $slots);
$smarty->assign('slots_hash',  $pollService->hashSlots($slots));
$smarty->assign('votes', $pollService->splitVotes($votes));
$smarty->assign('best_choices', $pollService->computeBestChoices($votes));
$smarty->assign('comments', $comments);
$smarty->assign('editingVoteId', $editingVoteId);
$smarty->assign('message', $message);
$smarty->assign('admin', false);
$smarty->assign('hidden', $poll->hidden);
$smarty->assign('editedVoteUniqueId', $editedVoteUniqueId);
$smarty->assign('readonly', true);

$smarty->display('studs.tpl');
