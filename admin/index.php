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
namespace Framadate;
session_start();

include_once __DIR__ . '/../app/inc/init.php';
include_once __DIR__ . '/../bandeaux.php';

// Ce fichier index.php se trouve dans le sous-repertoire ADMIN de Studs. Il sert à afficher l'intranet de studs
// pour modifier les sondages directement sans avoir reçu les mails. C'est l'interface d'aministration
// de l'application.

// Affichage des balises standards
Utils::print_header( _("Polls administrator") );
bandeau_titre(_("Polls administrator"));

$sondage=$connect->Execute("select * from sondage");

echo'
    <form action="' . Utils::get_server_name() . 'admin/index.php" method="POST">'."\n";
// Test et affichage du bouton de confirmation en cas de suppression de sondage
while($dsondage = $sondage->FetchNextObject(false)) {
    if (Utils::issetAndNoEmpty('supprimersondage'.$dsondage->id_sondage) === true) {
        echo '
        <div class="alert alert-warning text-center">
            <h2>'. _("Confirm removal of the poll ") .'"'.$dsondage->id_sondage.'</h2>
            <p><button class="btn btn-default" type="submit" value="1" name="annullesuppression">'._("Keep this poll!").'</button>
            <button type="submit" name="confirmesuppression'.$dsondage->id_sondage.'" value="1" class="btn btn-danger">'._("Remove this poll!").'</button></p>
        </div>';
    }

    // Traitement de la confirmation de suppression
    if (Utils::issetAndNoEmpty('confirmesuppression'.$dsondage->id_sondage) === true) {
        // On inclut la routine de suppression
        $date=date('H:i:s d/m/Y');

        if (Utils::remove_sondage($connect, $dsondage->id_sondage)) {
           // ecriture des traces dans le fichier de logs
           error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'logs_studs.txt');
        }
    }
}

$sondage=$connect->Execute("select * from sondage WHERE date_fin > DATE_SUB(now(), INTERVAL 3 MONTH) ORDER BY date_fin ASC");
$nbsondages=$sondage->RecordCount();

$btn_logs = (is_readable('logs_studs.txt')) ? '<a role="button" class="btn btn-default btn-xs pull-right" href="'.str_replace('/admin','', Utils::get_server_name()).'admin/logs_studs.txt">'. _("Logs") .'</a>' : '';

echo '<p>' . $nbsondages. ' ' . _("polls in the database at this time") . $btn_logs .'</p>'."\n";

// tableau qui affiche tous les sondages de la base
echo '<table class="table table-bordered">
    <tr align="center">
        <th scope="col">'. _("Poll ID") .'</th>
        <th scope="col">'. _("Format") .'</th>
        <th scope="col">'. _("Title") .'</th>
        <th scope="col">'. _("Author") .'</th>
        <th scope="col">'. _("Email") .'</th>
        <th scope="col">'. _("Expiration's date") .'</th>
        <th scope="col">'. _("Users") .'</th>
        <th scope="col" colspan="3">'. _("Actions") .'</th>
    </tr>'."\n";

$i = 0;
while($dsondage = $sondage->FetchNextObject(false)) {
    /* possible en 1 bonne requête dans $sondage */
    $sujets=$connect->Execute( "select * from sujet_studs where id_sondage='$dsondage->id_sondage'");
    $dsujets=$sujets->FetchObject(false);

    $user_studs=$connect->Execute( "select * from user_studs where id_sondage='$dsondage->id_sondage'");
    $nbuser=$user_studs->RecordCount();

    echo '
    <tr align="center">
        <td>'.$dsondage->id_sondage.'</td>
        <td>'.$dsondage->format.'</td>
        <td>'. stripslashes($dsondage->titre).'</td>
        <td>'.stripslashes($dsondage->nom_admin).'</td>
        <td>'.stripslashes($dsondage->mail_admin).'</td>';

    if (strtotime($dsondage->date_fin) > time()) {
        echo '
        <td>'.date("d/m/y",strtotime($dsondage->date_fin)).'</td>';
    } else {
        echo '
        <td><span class="text-danger">'.date("d/m/y",strtotime($dsondage->date_fin)).'</span></td>';
    }
    echo '
        <td>'.$nbuser.'</td>
        <td><a href="' . Utils::getUrlSondage($dsondage->id_sondage) . '" class="btn btn-link" title="'. _("See the poll") .'"><span class="glyphicon glyphicon-eye-open"></span><span class="sr-only">' . _("See the poll") . '</span></a></td>
        <td><a href="' . Utils::getUrlSondage($dsondage->id_sondage_admin, true) . '" class="btn btn-link" title="'. _("Change the poll") .'"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _("Change the poll") . '</span></a></td>
        <td><button type="submit" name="supprimersondage'.$dsondage->id_sondage.'" value="'. _("Remove the poll") .'" class="btn btn-link" title="'. _("Remove the poll") .'"><span class="glyphicon glyphicon-trash text-danger"></span><span class="sr-only">' . _("Remove the poll") . '</span></td>
    </tr>'."\n";
    $i++;
}

echo '</table></form>'."\n";

bandeau_pied(true);

// si on annule la suppression, rafraichissement de la page
if (Utils::issetAndNoEmpty('annulesuppression') === true) {
}
