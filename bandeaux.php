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

function framanav()
{
    if (file_exists($_SERVER['DOCUMENT_ROOT']."/nav/nav.js")) {
    echo "\n".'<!-- Framanav --> '."\n";;
    echo '<script src="/nav/nav.js" id="nav_js" type="text/javascript" charset="utf-8"></script>'."\n";
    echo '<!-- /Framanav --> '."\n";
    }
}

function gAnalytics() {
  if (GOOGLE_ANALYTICS_ID !== false) {
    echo '
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push([\'_setAccount\', \''.GOOGLE_ANALYTICS_ID.'\']);
  _gaq.push([\'_trackPageview\']);

  (function() {
    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>';
  }
}


//le logo
function logo ()
{
/*  if(defined('LOGOBANDEAU')) {
    echo '<div class="logo"><img src="/' . LOGOBANDEAU . '" height="74" alt=""></div>'."\n";
  }*/
}


#le bandeau principal
function bandeau_tete()
{
  if ( IMAGE_TITRE ) {

    echo '<div role="banner" class="bandeau"><h1><a href="'.str_replace('/admin','',get_server_name()).'" title="'._("Home").' - '.NOMAPPLICATION.'"><img src="'.get_server_name(). IMAGE_TITRE. '" title="'._("Home").' - '.NOMAPPLICATION.'" alt="'.NOMAPPLICATION.'"></a></h1></div>'."\n";

  } else {

    echo '<div role="banner" class="bandeau"><h1><a href="'.str_replace('/admin','',get_server_name()).'" title="'._("Home").' - '.NOMAPPLICATION.'">'.NOMAPPLICATION.'</a></h1></div>'."\n";

  } ;

}


// bandeaux de titre
function bandeau_titre($titre)
{
  echo '<p class="bandeautitre">'. $titre .'</p>'."\n";
}


function liste_lang()
{
  global $ALLOWED_LANGUAGES; global $lang;
  $str = '';
  foreach ($ALLOWED_LANGUAGES as $k => $v ) {
	if (substr($k,0,2)==$lang) { 
		$str .= '<option lang="'.substr($k,0,2).'" selected value="' . $k . '">' . $v . '</option>' . "\n" ;
	} else {
		$str .= '<option lang="'.substr($k,0,2).'" value="' . $k . '">' . $v . '</option>' . "\n" ;
	}
  }

  return $str;
}


#Les sous-bandeaux contenant les boutons de navigation
function sous_bandeau()
{
  /*echo '<div class="sousbandeau">' .
       '<a href="./">'. _("Home") .'</a>' .
       '<a href="' . getUrlSondage('aqg259dth55iuhwm').'">'. _("Example") .'</a>' .
       '<a href="http://contact.framasoft.org" target="_new">'. _("Contact") .'</a>' .
       //'<a href="/sources/sources.php">'. _("Sources") .'</a>' . //not implemented
       '<a href="/apropos.php">'. _("About") .'</a>' .
       '<a href="/admin/index.php">'. _("Admin") .'</a>' .
       '<span class="sousbandeau sousbandeaulangue">' .
       liste_lang() . '</span>'.
       '</div>' . "\n";*/
}


function sous_bandeau_admin()
{
  echo '<div class="sousbandeau">' .
       '<a class="button small gray" href="'.str_replace('/admin','',get_server_name()).'">'. _("Home") .'</a>';

  if(is_readable('logs_studs.txt')) {
    echo '<a role="button" class="button small gray" href="'.str_replace('/admin','',get_server_name()).'admin/logs_studs.txt">'. _("Logs") .'</a>';
  }

  echo '<a role="button" class="button small gray" href="'.str_replace('/admin','',get_server_name()).'scripts/nettoyage_sondage.php">'. _("Cleaning") .'</a>' .
       '<ul class="sousbandeau sousbandeaulangue">' .
       liste_lang() . '</ul>'.
       '</div>'."\n";

    gAnalytics();
}


function sous_bandeau_choix()
{
  /*echo '<div class="sousbandeau">' .
       '<a href="/">'. _("Home") .'</a>' .
       '</div>'."\n";*/
}


#les bandeaux de pied
function sur_bandeau_pied()
{
  echo '<div class="surbandeaupied"></div>'."\n";
}


function bandeau_pied()
{
  //echo '<div class="bandeaupied">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";
  echo '<div class="sousbandeau">' .
		'<ul>' .
       '<li><a class="button small gray" href="'.get_server_name().'">'. _("Home") .'</a></li>' .
       '<li><a class="button small gray" href="' . getUrlSondage('aqg259dth55iuhwm').'">'. _("Example") .'</a></li>' .
       '<li><a class="button small gray" href="http://contact.framasoft.org">'. _("Contact") .'</a></li>' .
       //'<a href="/sources/sources.php">'. _("Sources") .'</a>' . //not implemented
       '<li><a class="button small gray" href="'.get_server_name().'apropos.php">'. _("About") .'</a></li>' .
       //'<a class="button small gray" href="/admin/index.php">'. _("Admin") .'</a></li>' .
       '</ul>' .
       '<ul class="sousbandeau sousbandeaulangue"><li><form method="post" action=""><select name="lang" title="'. _("Change the language") .'" class="small white" >' .
       liste_lang() . '</select><input type="submit" value="OK" class="small white" /></form></li></ul>'.
       '</div>' . "\n";
    gAnalytics();
}


function bandeau_pied_mobile()
{
  /*echo '<div class="surbandeaupiedmobile"></div>'."\n" .
       '<div class="bandeaupiedmobile">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";*/
       echo '<div class="separateur">&nbsp;</div>';
  echo '<div class="sousbandeau">' .
		'<ul>' .
       '<li><a class="button small gray" href="'.get_server_name().'">'. _("Home") .'</a></li>' .
       '<li><a class="button small gray" href="' . getUrlSondage('aqg259dth55iuhwm').'">'. _("Example") .'</a></li>' .
       '<li><a class="button small gray" href="http://contact.framasoft.org">'. _("Contact") .'</a></li>' .
       //'<a href="/sources/sources.php">'. _("Sources") .'</a>' . //not implemented
       '<li><a class="button small gray" href="'.get_server_name().'apropos.php">'. _("About") .'</a></li>' .
       //'<a class="button small gray" href="/admin/index.php">'. _("Admin") .'</a></li>' .
       '</ul>' .
       '<ul class="sousbandeau sousbandeaulangue"><li><form method="post" action=""><select name="lang" title="'. _("Change the language") .'" class="small white" >' .
       liste_lang() . '</select><input type="submit" value="OK" class="small white" /></form></li></ul>'.
       '</div>' . "\n";
    gAnalytics();
}
