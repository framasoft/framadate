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


include '../bandeaux.php';

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">'."\n";
echo '<html lang="'.$lang.'">'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>Erreur !</title>'."\n";
echo '<link rel="stylesheet" type="text/css" href="../style.css">'."\n";
echo '</head>'."\n";
echo '<body>'."\n";
logo();
bandeau_tete();
bandeau_titre(_("Make your polls"));
echo '<div class=corpscentre>'."\n";
print "<H2>Vous n'avez pas l'autorisation de voir ce r&eacute;pertoire.<br> </H2>Vous devez, pour cela, initier votre connexion depuis une machine de l'Universit&eacute;.<br> Si vous avez un compte &agrave; l'Universit&eacute;, vous pouvez &eacute;galement utiliser le <a href=\"https://www-crc.u-strasbg.fr/osiris/services/vpn\">VPN s&eacute;curis&eacute;</a>.<br><br>"."\n";
print "Vous pouvez retourner &agrave; la page d'accueil de <a href=\"../index.php\"> ".NOMAPPLICATION."</A>."."\n";
echo '<br><br><br>'."\n";
echo '</div>'."\n";


// Affichage du bandeau de pied
sur_bandeau_pied();
bandeau_pied();
echo '</body>'."\n";
echo '</html>'."\n";
