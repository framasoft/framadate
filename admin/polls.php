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

use Framadate\Services\AdminPollService;
use Framadate\Services\LogService;
use Framadate\Services\PollService;
use Framadate\Services\SuperAdminService;
use Framadate\Utils;

include_once __DIR__ . '/../app/inc/init.php';
include_once __DIR__ . '/../bandeaux.php';

/* Services */
/*----------*/
$logService = new LogService();
$pollService = new PollService($connect, $logService);
$adminPollService = new AdminPollService($connect, $pollService, $logService);
$superAdminService = new SuperAdminService($connect);

// Ce fichier index.php se trouve dans le sous-repertoire ADMIN de Studs. Il sert à afficher l'intranet de studs
// pour modifier les sondages directement sans avoir reçu les mails. C'est l'interface d'aministration
// de l'application.

// Affichage des balises standards
Utils::print_header(_('Polls administrator'));
bandeau_titre(_('Polls administrator'));

$polls = $superAdminService->findAllPolls();

echo '<form action="' . Utils::get_server_name() . 'admin/index.php" method="POST">' . "\n";

// Test et affichage du bouton de confirmation en cas de suppression de sondage
foreach ($polls as $poll) {
    if (!empty($_POST['supprimersondage' . $poll->id])) {
        echo '
        <div class="alert alert-warning text-center">
            <h3>' . _("Confirm removal of the poll ") . '"' . $poll->id . '</h3>
            <p><button class="btn btn-default" type="submit" value="1" name="annullesuppression">' . _('Keep this poll!') . '</button>
            <button type="submit" name="confirmesuppression' . $poll->id . '" value="1" class="btn btn-danger">' . _('Remove this poll!') . '</button></p>
        </div>';
    }

    // Traitement de la confirmation de suppression
    if (!empty($_POST['confirmesuppression' . $poll->id])) {
        // On inclut la routine de suppression
        $date = date('H:i:s d/m/Y');

        $adminPollService->deleteEntirePoll($poll->id);
    }
}

$btn_logs = (is_readable('../' . LOG_FILE)) ? '<a role="button" class="btn btn-default btn-xs pull-right" href="' . Utils::get_server_name() . LOG_FILE . '">' . _("Logs") . '</a>' : '';

echo '<p>' . count($polls) . ' ' . _("polls in the database at this time") . $btn_logs . '</p>' . "\n";

// tableau qui affiche tous les sondages de la base
echo '<table class="table table-bordered">
    <tr align="center">
        <th scope="col">' . _('Poll ID') . '</th>
        <th scope="col">' . _('Format') . '</th>
        <th scope="col">' . _('Title') . '</th>
        <th scope="col">' . _('Author') . '</th>
        <th scope="col">' . _('Email') . '</th>
        <th scope="col">' . _('Expiration\'s date') . '</th>
        <th scope="col">' . _('Users') . '</th>
        <th scope="col" colspan="3">' . _('Actions') . '</th>
    </tr>' . "\n";

$i = 0;
foreach ($polls as $poll) {
    $nb_users = $pollService->countVotesByPollId($poll->id);

    if ($poll->format === 'D') {
        $format_html = '<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span><span class="sr-only">'. _('Date').'</span>';
    } else {
        $format_html = '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span><span class="sr-only">'. _('Classic').'</span>';
    }
    echo '
    <tr align="center">
        <td>' . $poll->id . '</td>
        <td>' . $format_html . '</td>
        <td>' . htmlentities($poll->title) . '</td>
        <td>' . htmlentities($poll->admin_name) . '</td>
        <td>' . htmlentities($poll->admin_mail) . '</td>';

    if (strtotime($poll->end_date) > time()) {
        echo '<td>' . date('d/m/y', strtotime($poll->end_date)) . '</td>';
    } else {
        echo '<td><span class="text-danger">' . date('d/m/y', strtotime($poll->end_date)) . '</span></td>';
    }
    echo '
        <td>' . $nb_users . '</td>
        <td><a href="' . Utils::getUrlSondage($poll->id) . '" class="btn btn-link" title="' . _('See the poll') . '"><span class="glyphicon glyphicon-eye-open"></span><span class="sr-only">' . _('See the poll') . '</span></a></td>
        <td><a href="' . Utils::getUrlSondage($poll->admin_id, true) . '" class="btn btn-link" title="' . _('Change the poll') . '"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _("Change the poll") . '</span></a></td>
        <td><button type="submit" name="supprimersondage' . $poll->id . '" value="' . _('Remove the poll') . '" class="btn btn-link" title="' . _("Remove the poll") . '"><span class="glyphicon glyphicon-trash text-danger"></span><span class="sr-only">' . _('Remove the poll') . '</span></td>
    </tr>' . "\n";
    ++$i;
}

echo '</table></form>' . "\n";

bandeau_pied(true);
