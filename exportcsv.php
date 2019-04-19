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
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

ob_start();

/* Variables */
/* --------- */

$poll_id = null;
$poll = null;

/* Services */
/*----------*/

$pollService = Services::poll();
$securityService = Services::security();

/* PAGE */
/* ---- */

if (!empty($_GET['poll'])) {
    $poll_id = filter_input(INPUT_GET, 'poll', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
    $poll = $pollService->findById($poll_id);
} else if (!empty($_GET['admin'])) {
    $admin_id = filter_input(INPUT_GET, 'admin', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => ADMIN_POLL_REGEX]]);
    $poll = $pollService->findByAdminId($admin_id);
    if ($poll) {
        $poll_id = $poll->id;
    }
}

if (!$poll) {
    $smarty->assign('error', __('Error', "This poll doesn't exist!"));
    $smarty->display('error.tpl');
    exit;
}

if (empty($admin_id)) {
    $forbiddenBecauseOfPassword = !$poll->results_publicly_visible && !$securityService->canAccessPoll($poll);
    $resultsAreHidden = $poll->hidden;

    if ($resultsAreHidden || $forbiddenBecauseOfPassword) {
        $smarty->assign('error', __('Error', 'Forbidden!'));
        $smarty->display('error.tpl');
        exit;
    }
}

$slots = $pollService->allSlotsByPoll($poll);
$votes = $pollService->allVotesByPollId($poll_id);

// CSV header
echo "\xEF\xBB\xBF"; // BOM character for UTF-8
if ($poll->format === 'D') {
    $titles_line = ',';
    $moments_line = ',';
    foreach ($slots as $slot) {
        $title = Utils::csvEscape($dateFormatter->format($slot->title));
        $moments = explode(',', $slot->moments);

        $titles_line .= str_repeat($title . ',', count($moments));
        $moments_line .= implode(',', array_map('\Framadate\Utils::csvEscape', $moments)) . ',';
    }
    echo $titles_line . "\r\n";
    echo $moments_line . "\r\n";
} else {
    echo ',';
    foreach ($slots as $slot) {
        echo Utils::markdown($slot->title, true) . ',';
    }
    echo "\r\n";
}
// END - CSV header

// Vote lines
foreach ($votes as $vote) {
    echo Utils::csvEscape($vote->name) . ',';
    $choices = str_split($vote->choices);
    foreach ($choices as $choice) {
        switch ($choice) {
            case 0:
                $text = __('Generic', 'No');
                break;
            case 1:
                $text = __('Generic', 'Under reserve');
                break;
            case 2:
                $text = __('Generic', 'Yes');
                break;
            default:
                $text = 'unkown';
        }
        echo Utils::csvEscape($text);
        echo ',';
    }
    echo "\r\n";
}
// END - Vote lines

// HTTP headers
$content = ob_get_clean();
$filesize = strlen($content);
$filename = Utils::cleanFilename($poll->title) . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Length: ' . $filesize);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=10');
// END - HTTP headers

echo $content;
