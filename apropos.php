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
echo '<html lang="'.$lang.'">'."\n";
echo '<head>'."\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">'."\n";
echo '<title>'._("About").' - '.NOMAPPLICATION.'</title>'."\n";
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
<ul>
  <li><a href="#faq">Questions fréquentes</a></li>
  <ul>
    <li><a href="#framadate">Qu'est-ce que Framadate
?</a></li>
    <li><a href="#studs">Quelles différences entre
Framadate et STUdS ?</a></li>
    <li><a href="#doodle">Quelles différences entre
Framadate et Doodle ?</a></li>
    <li><a href="#longevite">Mon sondage
restera-t-il longtemps en ligne ?</a></li>
  </ul>
  <li><a href="#mentions">Mentions légales</a></li>
  <li><a href="#credits">Crédits</a></li>
  <li><a href="#licence">Licence</a></li>
</ul>
<hr style="width: 100%; height: 2px;">
<h1><a name="faq"></a>Questions fréquentes</h1>
<h3><a name="framadate"></a>Qu'est-ce que
Framadate ?</h3>
Framadate est un service en ligne permettant de planifier un
rendez-vous rapidement et simplement. Aucune inscription préalable
n'est nécessaire.<br />
Framadate est un service du<a href="http://framasoft.org">
réseau Framasoft</a>, mis en place par<a
 href="http://fr.wikipedia.org/wiki/Framasoft"> l'association
Framasoft</a>.<br />
<h3><a name="studs"></a>Quelles différences
entre Framadate et STUdS ! ?</h3>
Framadate est un service basé sur le logiciel libre <a
 href="https://github.com/leblanc-simon/OpenSondage">OpenSondage</a>.
OpenSondage est lui-même basé sur le logiciel <a
 href="http://studs.u-strasbg.fr">STUdS !</a> développé
par l'Université de Strasbourg. <br />
Après avoir testé STUdS, nous avons décidé d'apporter de nombreuses
modifications, notamment ergonomiques, au code source existant.
L'ensemble de ces modifications ne pouvaient entrer dans le cadre
d'utilisation d'un logiciel déjà en production dans une université et
aurait été (fort logiquement) rejetté de la branche principale de
développement. C'est pourquoi nous avons préferer "<a
 href="http://fr.wikipedia.org/wiki/Fork_%28d%C3%A9veloppement_logiciel%29">forker</a>"
STUdS pour créer OpenSondage.<br />
<h3><a name="doodle"></a>Quelles différences
entre Framadate et <a href="http://doodle.com">Doodle</a>
?</h3>
Aujourd'hui, le danger pour le logiciel libre
ne provient plus de Microsoft ou d'Adobe et de leurs logiciels qu'on
installe sans avoir le code source, mais des applications web "dans les
nuages" proposés comme services par des entreprises.<br />
<br />
Cela pour au moins 4 raisons :<br />
1- <span style="font-weight: bold;">sécurité</span>
: aucune garantie ne peut vous être apportée quand au fait les données
soient correctement sauvegardées et protégées, ni que le<br />
code source "officiel" soit réellement celui que vous utilisez en ligne.<br />
2- <span style="font-weight: bold;">fiabilité/perennité</span>
: le service peut tomber en panne, et rien&nbsp;ne garanti que la
société Doodle sera toujours là demain et maintiendra le site<br />
3- <span style="font-weight: bold;">propriété des données</span>
: beaucoup d'entreprises s'autoproclament co-détentrices de vos
contenus "clouds" (ex: Facebook impose une clause de partage des droits
sur vos contenus, vos données, vos photos)<br />
4-<span style="font-weight: bold;"> vie privée</span>
: une entreprise - comme Doodle - doit gagner de l'argent (et il n'y a
aucun mal à cela). Mais si elle est en difficulté financière, elle peut
décider de changer ses conditions d'utilisation et vendre vos données à
des tiers (alors que Framasoft, asso loi 1901 d'intérêt général, n'aura
jamais d'intérêt à le faire).<br />
<br />
A cela s'ajoute le problème, plus éthique, de la publicité.<br />
<br />
Les problèmes 1 et 2 concernent aussi Framadate.org : rien ne garanti
la sécurité et la fiabilité du service (d'autant plus que les
administrateurs systèmes de Framasoft sont bénévoles).<br />
<br />
Par contre :<br />
- les problemes 3 et 4 ne sont pas d'actualité avec Framadate, exploité
par une association loi 1901<br />
- et surtout, Framadate fait partie d'un projet plus global
(Framatools) qui vise justement à sensibiliser le grand public à la
problématique du "cloud". Cela peut sembler paradoxal, mais bien que
proposant le service Framadate.org, nous allons surtout encourager les
organisations à installer leur propre instance du logiciel afin de
maitriser totalement leurs données.<br />
<br />
Bref, oui Framadate est inspiré de Doodle.com, et oui Doodle est un
excellent service. Mais Doodle reste une "boite noire" pour
l'utilisateur final qui va sur doodle.com. Framadate.org essaie de
répondre, modestement, à cette problématique en montrant que des
alternatives libres existent et qu'on peut les installer "chez soi".<br />
<h3><a name="longevite"></a>Mon sondage
restera-t-il longtemps en ligne ?</h3>
Le service Framadate est proposée gratuitement par l'association
Framasoft.<br />
Framasoft
s'engage à maintenir le service "aussi longtemps que possible", mais ne
peut fournir aucune garantie de date. Si cela ne vous convient pas,
nous vous
encourageons sincèrement à installer vous-même Framadate et à maintenir
vous-même votre propre service.
<hr style="width: 100%; height: 2px;">
<h1><a name="mentions"></a>Mentions légales<br />
</h1>
<h2>Éditeur et Responsable de la publication</h2>
<p>Editeur : Association Framasoft (cf "Hébergement")</p>
<p>Responsable de la publication : Christophe Masutti</p>
<p>Vous pouvez rentrer en contact avec l'Editeur et le
Responsable de la publication en passant par la page "<a
 href="http://contact.framasoft.org">contact</a>".</p>
<p>Les propos tenus sur ce site ne représentent que et uniquement
l’opinion de leur auteur, et n’engagent pas l'association Framasoft,
les sociétés, entreprises ou collectifs auxquels il contribue ou dont
il peut être associé ou employé.</p>
<h2>Hébergement</h2>
<p>Ce site est hébergé par Framasoft, 10 bis rue Jangot 69007 Lyon, France.
Cet hébergeur possède à ce jour les éléments d’identification
personnelle concernant l'Éditeur (voir <a
 href="http://www.framasoft.net/article4736.html">http://www.framasoft.net/article4736.html</a>).</p>
<h2>Données personnelles</h2>
<p>Les données personnelles collectées par Framadate sont
uniquement destinées à un usage interne. En aucun cas ces données ne
seront cédées ou vendues à des tiers.
Conformément à l’article 39 de la loi du 6 janvier 1978 relative à
l’informatique, aux fichiers et aux libertés, vous avez un droit
d’accès, de modification et d’opposition sur vos données personnelles
enregistrées par le blog.
Dans ce cas, utilisez le formulaire de contact.</p>
<h2>Conditions de modération/suppression de sondages</h2>
<p>Les sondages de Framadate bénéficient d'une URL aléatoire,
mais publique. Si vous souhaitez supprimer un sondage, utilisez
l'adresse d'aministration fournie à la création. Vous pouvez
exceptionnellement demander la suppression d'un sondage en utilisant la
page de contact.</p>
<h2>Notification des contenus litigieux</h2>
<p>Conformément à l’article 6 I 5° LCEN, la connaissance des
contenus litigieux est présumée acquise par L’Éditeur lorsqu’il lui est
notifié, par lettre recommandée avec accusé de réception adressée au
siège social de L’Éditeur, la totalité des éléments suivants (i) la
date de la notification&nbsp;; (ii) si le notifiant est une
personne physique&nbsp;: ses nom, prénoms, profession, domicile,
nationalité, date et lieu de naissance&nbsp;; si le notifiant est
une personne morale&nbsp;: sa forme, sa dénomination, son siège
social et l’organe qui la représente légalement&nbsp;; (iii) les
nom et domicile du destinataire ou, s’il s’agit d’une personne morale,
sa dénomination et son siège social&nbsp;; (iv) la description des
faits litigieux et leur localisation précise&nbsp;; (v) les motifs
pour lesquels le contenu doit être retiré, comprenant la mention des
dispositions légales et des justifications de faits&nbsp;; (vi) la
copie de la correspondance adressée à l’auteur ou à l’éditeur des
informations ou activités litigieuses demandant leur interruption, leur
retrait ou leur modification, ou la justification de ce que l’auteur ou
l’éditeur n’a pu être contacté.</p>
<p>A défaut d’envoi de la totalité de ces éléments, la
notification ne sera pas prise en compte par L’Éditeur et ce dernier ne
pourra en conséquence être présumé informé d’un contenu litigieux.</p>
<p>L’Éditeur se réserve le droit d’engager des poursuites à
l’encontre de toute personne ayant usé abusivement du droit réservé par
l’article 6 I 4° LCEN. L’Éditeur vous rappelle que toute personne qui
aurait présenté un contenu ou une activité comme étant illicite dans le
but d’en obtenir le retrait ou d’en faire cesser la diffusion alors
qu’elle a connaissance du caractère inexact de cette information, est
susceptible d’encourir une peine d’un an d’emprisonnement et de 15.000
€uros d’amende.</p>
<h2>Licences, droits de reproduction</h2>
<p>L'application Framadate, basé sur le logiciel OpenSondage,
lui-même basé sur STUdS, est publiée sous licence libre <a
 href="http://www.cecill.info/licences.fr.html">CeCILL-B</a>.
Les contenus (sondages) sont publiés sous licence Creative Commons
BY-SA. Cela signifie que si l'adresse de votre sondage est connue d'un
individu, vous autorisez cette personne à utiliser, partager, modifier
votre sondage. Si vous souhaitez des sondages 100% privés et avec votre
propre licence, installez votre propre logiciel de sondage et
n'utilisez pas Framadate.org.</p>
<hr style="width: 100%; height: 2px;">
<h2><a name="credits"></a>Crédits</h2>
<b>Application d'origine</b><br />
<br />
L'application Framadate est une instance du logiciel <b><a
 href="http://studs.u-strasbg.fr">STUdS !</a></b>
développé à l'Université de Strasbourg depuis 2008.<br />
<br />
Pour les besoins de Framadate, STUdS a fait l'objet d'un fork par
l'équipe Framasoft. Les sources sont disponibles sur le Github <a
 href="https://github.com/leblanc-simon/OpenSondage">OpenSondage</a>.<br />
<br />
<b>Technologies utilisées</b><br />
<br />
- <a href="http://www.php.net/">PHP</a><br />
- <a href="http://www.postgresql.org/">MySQL</a><br />
- <a href="http://www.apache.org/">Apache</a><br />
- <a href="http://subversion.tigris.org/">Subversion</a><br />
- <a href="http://www.kigkonsult.se/iCalcreator/">iCalcreator</a><br />
- <a href="http://www.fpdf.org/">FPDF</a><br />
- Icônes : <a href="http://deleket.deviantart.com/">Deleket</a>,
<a href="http://pixel-mixer.com">PixelMixer</a> et <a
 href="http://dryicons.com">DryIcons</a><br />
<br />
<b>Compatibilités des navigateurs</b><br />
<br />
- <a href="http://www.mozilla.com/firefox/">Firefox</a><br />
- <a href="http://www.opera.com/">Opéra</a><br />
- <a href="http://www.konqueror.org/">Konqueror</a><br />
- <a href="http://www.jikos.cz/%7Emikulas/links/">Links</a><br />
- <a href="http://www.apple.com/fr/safari/">Safari</a><br />
- <a href="http://www.mozilla.com/firefox/">IE</a><br />
<br />
<b>Validations des pages</b><br />
<br />
- Toutes les pages disposent de la validation HTML 4.01 Strict du W3C. <br />
- La CSS dispose de la validation CSS 2.1 du W3C.
<p><img src="http://www.w3.org/Icons/valid-html401-blue"
 alt="Valid HTML 4.01 Strict" height="31" width="88"><img
 style="border: 0pt none ; width: 88px; height: 31px;"
 src="http://jigsaw.w3.org/css-validator/images/vcss-blue"
 alt="CSS Valide !">
</p>
<b>Propositions améliorations de Framadate</b><br />
<br />
Si quelque chose venait à vous manquer, vous pouvez nous en faire part
via le <a href="http://contact.framasoft.org">formulaire en ligne</a>.
<br />
Les dernières améliorations de Framadate sont visibles dans le fichier <a
 href="CHANGELOG">CHANGELOG</a>.<br />
<br />
<b>Remerciements</b><br />
<br />
<b><a href="http://studs.u-strasbg.fr">STUdS !</a></b>&nbsp;:
Pour leurs contributions techniques ou ergonomiques : Guy, Christophe,
Julien, Pierre, Romaric, Matthieu, Catherine, Christine, Olivier,
Emmanuel et Florence <br />
<a style="font-weight: bold;"
 href="https://github.com/leblanc-simon/OpenSondage">Framadate</a>
: &nbsp;Simon Leblanc (développement principal), Pierre-Yves Gosset
(développement, graphisme), la communauté Framasoft.<br />
<br />
<h2><b><a name="licence"></a>Licence</b></h2>
Framadate est placé, comme <b><a
 href="http://studs.u-strasbg.fr">STUdS !</a>,</b>
sous la licence logicielle libre <a
 href="http://www.cecill.info/licences.fr.html">CeCILL-B</a>.<br />
<br />
mentions;

echo '</div>'."\n";

bandeau_pied_mobile();
echo '</form>'."\n";
echo '</body>'."\n";
echo '</html>'."\n";
