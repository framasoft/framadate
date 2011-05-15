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

include_once('variables.php');
include_once( 'i18n.php' );
if (file_exists('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}

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
echo '<form name=formulaire action="apropos.php" method="POST">'."\n";

//bandeaux de tete
logo();
bandeau_tete();
bandeau_titre(_("About"));
sous_bandeau();

echo '<div class=corps>'."\n";

if (NOMAPPLICATION!="STUdS !") {
  echo '<b>Application d\'origine</b><br><br>'."\n";
  echo 'L\'application '.NOMAPPLICATION.' est une instance du logiciel <b><a href ="http://studs.u-strasbg.fr">STUdS !</a></b> d&eacute;velopp&eacute; &agrave; l\'Universit&eacute; de Strasbourg depuis 2008.<br><br>'."\n";
}

echo '<b>Licence Logicielle de '.NOMAPPLICATION.'</b><br><br>'."\n";
echo NOMAPPLICATION.' est plac&eacute; sous la licence logicielle libre <a href="http://www.cecill.info/licences.fr.html">CeCILL-B</a>.<br><br>'."\n";

echo '<b>Technologies utilis&eacute;es</b><br><br>'."\n";
echo '- <a href="http://www.php.net/">PHP</a><br>'."\n";
echo '- <a href="http://www.postgresql.org/">PostgreSQL</a><br>'."\n";
echo '- <a href="http://www.apache.org/">Apache</a><br>'."\n";
echo '- <a href="http://subversion.tigris.org/">Subversion</a><br>'."\n";
echo '- <a href="http://www.kigkonsult.se/iCalcreator/">iCalcreator</a><br>'."\n";
echo '- <a href="http://www.fpdf.org/">FPDF</a><br>'."\n";
echo '- Ic&ocirc;nes : <a href="http://deleket.deviantart.com/">Deleket</a>, <a href ="http://pixel-mixer.com">PixelMixer</a> et <a href="http://dryicons.com">DryIcons</a><br><br>'."\n";

echo '<b>Compatibilit&eacute;s des navigateurs</b><br><br>'."\n";
echo '- <a href="http://www.mozilla.com/firefox/">Firefox</a><br>'."\n";
echo '- <a href="http://www.opera.com/">Op&eacute;ra</a><br>'."\n";
echo '- <a href="http://www.konqueror.org/">Konqueror</a><br>'."\n";
echo '- <a href="http://www.jikos.cz/~mikulas/links/">Links</a><br>'."\n";
echo '- <a href="http://www.apple.com/fr/safari/">Safari</a><br>'."\n";
echo '- <a href="http://www.mozilla.com/firefox/">IE</a><br><br>'."\n";

echo '<b>Validations des pages</b><br><br>'."\n";
echo '- Toutes les pages disposent de la validation HTML 4.01 Strict du W3C. <br>- La CSS dispose de la validation CSS 2.1 du W3C.'."\n";
echo '<p>'."\n";
echo '<img src="http://www.w3.org/Icons/valid-html401-blue" alt="Valid HTML 4.01 Strict" height="31" width="88"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valide !">'."\n";
echo'</p>'."\n";

echo '<b>Propositions am&eacute;liorations de '.NOMAPPLICATION.'</b><br><br>'."\n";
echo 'Si quelquechose venait &agrave; vous manquer, vous pouvez nous en faire part via le <a href="contacts.php">formulaire en ligne</a>. <br>'."\n";
echo 'Les derni&egrave;res am&eacute;liorations de '.NOMAPPLICATION.' sont visibles dans le fichier <a href="CHANGELOG">CHANGELOG</a>.<br><br>'."\n";

echo '<b>Remerciements</b><br><br>'."\n";
echo 'Pour leurs contributions techniques ou ergonomiques : Guy, Christophe, Julien, Pierre, Romaric, Matthieu, Catherine, Christine, Olivier, Emmanuel et Florence <br><br>'."\n";

echo '</div>'."\n";

bandeau_pied_mobile();
echo '</form>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";