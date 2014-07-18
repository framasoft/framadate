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
include_once('fonctions.php');
if (is_readable('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

session_start();

//affichage de la page
echo '<!DOCTYPE html>'."\n";
echo '<html lang="'.$lang.'">'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>'._("Home").' - '.NOMAPPLICATION.'</title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

framanav();

//bandeaux de tete
logo();
bandeau_tete();
bandeau_titre(_("Organiser des rendez-vous simplement, librement."));
sous_bandeau();

echo '<div class=corps>'."\n";
#echo '<p><b>'.NOMAPPLICATION.'<br />'. _("What is it about?") .'</b></p>';
#echo '<p>'. _("Making polls to schedule meetings or events, quickly and easily. <br /> You can also run polls to determine what will be your next meeting place, the meeting topic or anything like the country you would like to visit during your next holidays.") .'</p>'."\n".'<br />'."\n";
#echo '<div class="nouveau_sondage"><b>'. _("Make a poll") .'</b>';
#     '<span>' .
#     '<a href="/infos_sondage.php"><img alt="' . _('Make a poll') . '" src="images/next-32.png" /></a>' .
#     '</span>';
#echo '</div>' . "\n";

echo '<div class="index_date">';
echo '<p><a href="'.get_server_name().'infos_sondage.php?choix_sondage=date" role="button"><img class="opacity" src="'.get_server_name().'images/date.png" alt="" />';
echo '<br /><span class="button orange bigrounded"><strong><img src="'.get_server_name().'images/calendar-32.png" alt="" />'
    . _('Schedule an event') . '</strong></span></a></p>';
echo '</div>';

echo '<div class="index_sondage">';
echo '<p><a href="'.get_server_name().'infos_sondage.php?choix_sondage=autre" role="button"><img alt="" class="opacity" src="'.get_server_name().'images/sondage2.png" />';
echo '<br /><span class="button blue bigrounded"><strong><img src="'.get_server_name().'images/chart-32.png" alt="" />'. _('Make a poll') . '</strong></span></a></p>';
echo '</div>';


echo '<div style="clear:both;"></div>'."\n";

echo '</div>'."\n";
//bandeau de pied
//sur_bandeau_pied();
bandeau_pied();

echo '</body>'."\n";
echo '</html>'."\n";
