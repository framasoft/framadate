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
use Framadate\Utils;

include_once __DIR__ . '/../app/inc/init.php';

$mailService = Services::mail();
$notificationService = Services::notification();
$pollService = Services::poll();
$sessionService = Services::session();

$result = false;
$message = null;
$poll = null;
$poll_id = null;
$email = null;

if (!empty($_POST['poll'])) {
    $poll_id = filter_input(INPUT_POST, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
}

$token = $sessionService->get("Common", SESSION_EDIT_LINK_TOKEN);
$token_form_value = empty($_POST['token']) ? null : $_POST['token'];
$editedVoteUniqueId = filter_input(INPUT_POST, 'editedVoteUniqueId', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
if (is_null($poll) || $config['use_smtp'] === false || is_null($token) || is_null($token_form_value)
    || !$token->check($token_form_value) || is_null($editedVoteUniqueId)) {
    $message = new Message('error', __('Error', 'Something has gone wrong...'));
}

if (is_null($message)) {
    $email = $mailService->isValidEmail($_POST['email']);
    if (is_null($email)) {
        $message = new Message('error', __('EditLink', 'The email address is not correct.'));
    }
}

if (is_null($message)) {
    $time = $sessionService->get("Common", SESSION_EDIT_LINK_TIME);

    if (!empty($time)) {
        $remainingTime = TIME_EDIT_LINK_EMAIL - (time() - $time);

        if ($remainingTime > 0) {
            $message = new Message('error', __f('EditLink', 'Please wait %d seconds before we can send an email to you then try again.', $remainingTime));
        }
    }
}

if (is_null($message)) {
    $notificationService->sendEditedVoteNotification($email, $poll, $poll_id, $editedVoteUniqueId);
    $sessionService->remove("Common", SESSION_EDIT_LINK_TOKEN);
    $sessionService->set("Common", SESSION_EDIT_LINK_TIME, time());

    $message = new Message('success', __('EditLink', 'Your reminder has been successfully sent!'));
    $result = true;
}

$smarty->error_reporting = E_ALL & ~E_NOTICE;

$response = ['result' => $result, 'message' => $message];

echo json_encode($response);
