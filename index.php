<?php
/* This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 *
 * Authors of STUdS (initial project) : Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of OpenSondage : Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL_V2.1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs d'OpenSondage : Framasoft (https://github.com/framasoft)
 */
include_once __DIR__ . '/app/inc/functions.php';

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

session_start();

//affichage de la page
print_header ( _("Home") );
bandeau_titre(_("Organiser des rendez-vous simplement, librement."));

echo '
        <div class="index_date">
            <p><a href="'.get_server_name().'infos_sondage.php?choix_sondage=date" role="button">
                <img class="opacity" src="'.get_server_name().'images/date.png" alt="" />
                <br /><span class="button orange bigrounded"><strong><img src="'.get_server_name().'images/calendar-32.png" alt="" />'
                . _('Schedule an event') . '</strong></span>
            </a></p>
        </div>
        <div class="index_sondage">
            <p><a href="'.get_server_name().'infos_sondage.php?choix_sondage=autre" role="button">
                <img alt="" class="opacity" src="'.get_server_name().'images/sondage2.png" />
                <br /><span class="button blue bigrounded"><strong><img src="'.get_server_name().'images/chart-32.png" alt="" />
                '. _('Make a poll') . '</strong></span>
            </a></p>
        </div>'."\n";
    
//bandeau de pied
bandeau_pied();
