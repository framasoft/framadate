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

include_once __DIR__ . '/app/inc/init.php';

/* SERVICES */
/* -------- */

$notificationService = Services::notification();
$pollService = Services::poll();

/* PAGE */
/* ---- */
$message = null;

if (!empty($_POST['mail'])) {
    $mail = filter_input(INPUT_POST, 'mail', FILTER_VALIDATE_EMAIL);
    if ($mail) {
        $polls = $pollService->findAllByAdminMail($mail);

        if (count($polls) > 0) {
            $notificationService->sendFindPollsByMailNotification($mail, $polls);
            $message = new Message('success', t('FindPolls', 'Polls sent'));
        } else {
            $message = new Message('warning', t('Error', 'No polls found'));
        }
    } else {
        $message = new Message('danger', t('Error', 'Something is wrong with the format'));
    }
}

$smarty->assign('title', t('Homepage', 'Where are my polls?'));
$smarty->assign('message', $message);
$smarty->assign('locale', $locale);

$smarty->display('find_polls.tpl');
