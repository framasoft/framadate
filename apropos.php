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

framanav();

//debut du formulaire
echo '<form name=formulaire action="apropos.php" method="POST">'."\n";

//bandeaux de tete
logo();
bandeau_tete();
bandeau_titre(_("About"));
sous_bandeau();

echo '<div class=corps>'."\n";

echo <<<mentions
<h2>Éditeur et Responsable de la publication</h2>


<p>Alexis Kauffmann (<a href="/contact.php">contact</a>)</p>


<p>Les propos tenus sur ce site ne représentent que et uniquement l’opinion de leur auteur, et n’engagent pas les sociétés, entreprises ou collectifs auxquels il contribue ou dont il peut être associé ou employé.</p>


<h2>Hébergement</h2>


<p>Ce site est hébergé par Framasoft, c/o T. CEZARD, 5 avenue Stephen Pichon 75013 Paris, France.
Cet hébergeur possède à ce jour les éléments d’identification personnelle concernant l'Éditeur (voir <a href="http://www.framasoft.net/article4736.html">http://www.framasoft.net/article4736.html</a>).</p>


<h2>Données personnelles</h2>


<p>Les données personnelles collectées par Framadate sont uniquement destinées à un usage interne. En aucun cas ces données ne seront cédées ou vendues à des tiers.
Conformément à l’article 39 de la loi du 6 janvier 1978 relative à l’informatique, aux fichiers et aux libertés, vous avez un droit d’accès, de modification et d’opposition sur vos données personnelles enregistrées par le blog.
Dans ce cas, utilisez le formulaire de contact.</p>


<h2>Conditions de modération/suppression de sondages</h2>


<p>Les sondages de Framadate bénéficient d'une URL aléatoire, mais publique. Si vous souhaitez supprimer un sondage, utilisez l'adresse d'aministration fournie à la création. Vous pouvez exceptionnellement demander la suppression d'un sondage en utilisant la page de contact.</p>

<h2>Notification des contenus litigieux</h2>

<p>Conformément à l’article 6 I 5° LCEN, la connaissance des contenus litigieux est présumée acquise par L’Éditeur lorsqu&#8217;il lui est notifié, par lettre recommandée avec accusé de réception adressée au siège social de L’Éditeur, la totalité des éléments suivants (i) la date de la notification&#160;; (ii) si le notifiant est une personne physique&#160;: ses nom, prénoms, profession, domicile, nationalité, date et lieu de naissance&#160;; si le notifiant est une personne morale&#160;: sa forme, sa dénomination, son siège social et l&#8217;organe qui la représente légalement&#160;; (iii) les nom et domicile du destinataire ou, s&#8217;il s&#8217;agit d&#8217;une personne morale, sa dénomination et son siège social&#160;; (iv) la description des faits litigieux et leur localisation précise&#160;; (v) les motifs pour lesquels le contenu doit être retiré, comprenant la mention des dispositions légales et des justifications de faits&#160;; (vi) la copie de la correspondance adressée à l&#8217;auteur ou à l&#8217;éditeur des informations ou activités litigieuses demandant leur interruption, leur retrait ou leur modification, ou la justification de ce que l&#8217;auteur ou l&#8217;éditeur n&#8217;a pu être contacté.</p>


<p>A défaut d’envoi de la totalité de ces éléments, la notification ne sera pas prise en compte par L’Éditeur et ce dernier ne pourra en conséquence être présumé informé d’un contenu litigieux.</p>


<p>L’Éditeur se réserve le droit d’engager des poursuites à l’encontre de toute personne ayant usé abusivement du droit réservé par l’article 6 I 4° LCEN. L’Éditeur vous rappelle que toute personne qui aurait présenté un contenu ou une activité comme étant illicite dans le but d&#8217;en obtenir le retrait ou d&#8217;en faire cesser la diffusion alors qu&#8217;elle a connaissance du caractère inexact de cette information, est susceptible d’encourir une peine d&#8217;un an d&#8217;emprisonnement et de 15.000 €uros d&#8217;amende.</p>


<h2>Licences, droits de reproduction</h2>


<p>L'application Framadate, basé sur le logiciel OpenSondage, lui-même basé sur STUdS, est publiée sous licence CECILL-B. Les contenus (sondages) sont publiés sous licence Creative Commons BY-SA. Cela signifie que si l'adresse de votre sondage est connue d'un individu, vous autorisez cette personne à utiliser, partager, modifier votre sondage. Si vous souhaitez des sondages 100% privés et avec votre propre licence, installez votre propre logiciel de sondage et n'utilisez pas Framadate.org.</p>


<h2>Crédits</h2>

mentions;


if (NOMAPPLICATION!="STUdS !") {
  echo '<b>Application d\'origine</b><br><br>'."\n";
  echo 'L\'application '.NOMAPPLICATION.' est une instance du logiciel <b><a href ="http://studs.u-strasbg.fr">STUdS !</a></b> d&eacute;velopp&eacute; &agrave; l\'Universit&eacute; de Strasbourg depuis 2008.<br><br>'."\n";
  echo "Pour les besoins de Framadate, STUdS a fait l'objet d'un fork par l'équipe Framasoft. Les sources sont disponibles sur le Github <a href='https://github.com/leblanc-simon/OpenSondage'>OpenSondage</a>.<br/><br/>\n";
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