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

include_once __DIR__ . '/app/inc/init.php';

include_once('bandeaux.php');

Utils::print_header ( _('Maintenance') );
bandeau_titre( _('Maintenance') );

echo '
    <div class="alert alert-warning">
        <h2>'. _('The application') .NOMAPPLICATION . _('is currently under maintenance. ') . '</h2>'
        '<p>' . _('Thank you for your understanding.') . '</p>
    </div>'."\n";

// Affichage du bandeau de pied
bandeau_pied();
