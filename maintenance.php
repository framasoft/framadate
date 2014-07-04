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
include_once('bandeaux.php');

echo '<!DOCTYPE html>'."\n";
echo '<html lang="'.$lang.'">'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>Maintenance '.NOMAPPLICATION.' </title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="'.get_server_name().'style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";
logo();
bandeau_tete();

echo '<div class=corpscentre>'."\n";
print "<H2>L'application ".NOMAPPLICATION." est pour l'instant en maintenance.<br /> </H2>"."\n";
print "Merci de votre compr&eacute;hension."."\n";
echo '<br /><br /><br />'."\n";
echo '</div>'."\n";

// Affichage du bandeau de pied
sur_bandeau_pied();
bandeau_pied();
echo '</body>'."\n";
echo '</html>'."\n";
