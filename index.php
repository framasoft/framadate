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

include_once('fonctions.php');
if (is_readable('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

session_start();

//affichage de la page
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
echo '<html>'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>'.NOMAPPLICATION.'</title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

//debut du formulaire
echo '<form name=formulaire action="index.php" method="POST">'."\n";

//bandeaux de tete
logo();
bandeau_tete();
bandeau_titre(_("Make your polls"));
sous_bandeau();

echo '<div class=corps>'."\n";
echo '<p><b>'.NOMAPPLICATION.'<br>'. _("What is it about?") .'</b></p>';
echo '<p>'. _("Making polls to schedule meetings or events, quickly and easily. <br> You can also run polls to determine what will be your next meeting place, the meeting topic or anything like the country you would like to visit during your next holidays.") .'</p>'."\n".'<br>'."\n";
echo '<div class="nouveau_sondage"><b>'. _("Make a poll") .'</b>' .
     '<span>' .
     '<a href="' . get_server_name() . 'infos_sondage.php"><img alt="' . _('Make a poll') . '" src="images/next-32.png" /></a>' .
     '</span></div>' . "\n";

echo '<br>'."\n";
echo '<br><br>'."\n";
echo '</div>'."\n";
echo '</form>'."\n";
//bandeau de pied
//sur_bandeau_pied();
bandeau_pied();

echo '</body>'."\n";
echo '</html>'."\n";