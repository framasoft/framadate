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

use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

session_start();

// affichage de la page
Utils::print_header( _("Home") );
bandeau_titre(_("Organiser des rendez-vous simplement, librement."));
echo '
        <div class="row text-center">
            <div class="col-md-6">
                <p><a href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=date" role="button">
                    <img class="opacity" src="'.Utils::get_server_name().'images/date.png" alt="" />
                    <br /><span class="btn btn-warning btn-lg"><span class="glyphicon glyphicon-calendar"></span>
                    '. _('Schedule an event') . '</span>
                </a></p>
            </div>
            <div class="col-md-6">
                <p><a href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=autre" role="button">
                    <img alt="" class="opacity" src="'.Utils::get_server_name().'images/classic.png" />
                    <br /><span class="btn btn-primary btn-lg"><span class="glyphicon glyphicon-stats"></span>
                    '. _('Make a poll') . '</span>
                </a></p>
            </div>
        </div>
        <div class="row text-center">
                <p>'. _("or") .' <a href="' . Utils::getUrlSondage('aqg259dth55iuhwm').'">'. _("view an example") .'</a></p>
        </div>'."\n";

bandeau_pied();
