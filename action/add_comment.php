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
use Framadate\Message;
use Framadate\Services\NotificationService;

include_once __DIR__ . '/../app/inc/init.php';

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;
$message = null;
$result = false;
$comments = [];
$is_admin = false;

/* Services */
/*----------*/

$inputService = Services::input();
$notificationService = Services::notification();
$pollService = Services::poll();
$securityService = Services::security();

/* PAGE */
/* ---- */

if (!empty($_POST['poll'])) {
    $poll_id = filter_input(INPUT_POST, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

if (!empty($_POST['poll_admin'])) {
    $admin_poll_id = filter_input(INPUT_POST, 'poll_admin', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    if (strlen($admin_poll_id) === 24) {
        $is_admin = ($pollService->findByAdminId($admin_poll_id) !== null);
    }
}

if (!$poll) {
    $message = new Message('error',  __('Error', "This poll doesn't exist!"));
} else if ($poll && !$securityService->canAccessPoll($poll) && !$is_admin) {
    $message = new Message('error',  __('Password', 'Wrong password'));
} else {
    $name = $inputService->filterName($_POST['name']);
    $comment = $inputService->filterComment($_POST['comment']);

    if ($name === null) {
        $message = new Message('danger', __('Error', 'The name is invalid.'));
    }

    if ($message === null) {
        // Add comment
        $result = $pollService->addComment($poll_id, $name, $comment);
        if ($result) {
            $message = new Message('success', __('Comments', 'Comment saved'));
            $notificationService->sendUpdateNotification($poll, NotificationService::ADD_COMMENT, $name);
        } else {
            $message = new Message('danger', __('Error', 'Comment failed'));
        }
    }
    $comments = $pollService->allCommentsByPollId($poll_id);
}

$smarty->error_reporting = E_ALL & ~E_NOTICE;
$smarty->assign('comments', $comments);
$comments_html = $smarty->fetch('part/comments_list.tpl');

$response = ['result' => $result, 'message' => $message, 'comments' => $comments_html];

echo json_encode($response);
