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
use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\InputService;
use Framadate\Services\MailService;
use Framadate\Services\NotificationService;
use Framadate\Services\SecurityService;
use Framadate\Message;

include_once __DIR__ . '/../app/inc/init.php';

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;
$message = null;
$result = false;
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

if (!empty($_POST['poll'])) {
    $poll_id = filter_input(INPUT_POST, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

if (!$poll) {
    $message = new Message('error',  __('Error', 'This poll doesn\'t exist !'));
} else if ($poll && !$securityService->canAccessPoll($poll)) {
    $message = new Message('error',  __('Password', 'Wrong password'));
} else {
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
            $notificationService->sendUpdateNotification($poll, $mailService, $name, NotificationService::ADD_COMMENT);
        } else {
            $message = new Message('danger', __('Error', 'Comment failed'));
        }
    }
    $comments = $pollService->allCommentsByPollId($poll_id);
}

$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->assign('comments', $comments);
$comments_html = $smarty->fetch('part/comments_list.tpl');

$response = array('result' => $result, 'message' => $message, 'comments' => $comments_html);

echo json_encode($response);
