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

include_once __DIR__ . '/../app/inc/init.php';

//recuperation de la date
$date_courante=date("U");
$date=date('H:i:s d/m/Y:');

//ouverture de la connection avec la base SQL
$sondage=$connect->Execute("select * from sondage");

while ($dsondage=$sondage->FetchNextObject(false)) {
  if ($date_courante > strtotime($dsondage->date_fin)) {
    //destruction des données dans la base

    if (Utils::remove_sondage($connect, $dsondage->id_sondage)) {

      // ecriture des traces dans le fichier de logs
      error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, '../admin/logs_studs.txt');

    }

  }
}
