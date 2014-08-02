<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use,
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info".
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================



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

        if (Utils::remove_sondage($connect, $dsondage->id_sondage) {
           // ecriture des traces dans le fichier de logs
           error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'logs_studs.txt');
        }
    }
}

$sondage=$connect->Execute("select * from sondage WHERE date_fin > DATE_SUB(now(), INTERVAL 3 MONTH) ORDER BY date_fin ASC");
$nbsondages=$sondage->RecordCount();

echo '<p>' . $nbsondages. ' ' . _("polls in the database at this time") .'</p>'."\n";

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
        <td><a href="' . Utils::getUrlSondage($dsondage->id_sondage) . '" class="btn btn-link" title="'. _("See the poll") .'"><span class="glyphicon glyphicon-eye-open"></span></a></td>
        <td><a href="' . Utils::getUrlSondage($dsondage->id_sondage_admin, true) . '" class="btn btn-link" title="'. _("Change the poll") .'"><span class="glyphicon glyphicon-pencil"></span></a></td>
        <td><button type="submit" name="supprimersondage'.$dsondage->id_sondage.'" value="'. _("Remove the poll") .'" class="btn btn-link" title="'. _("Remove the poll") .'"><span class="glyphicon glyphicon-trash text-danger"></span></td>
    </tr>'."\n";
    $i++;
}

echo '</table></form>'."\n";

bandeau_pied(true);

// si on annule la suppression, rafraichissement de la page
if (Utils::issetAndNoEmpty('annulesuppression') === true) {
}
