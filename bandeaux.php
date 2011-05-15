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

// pour get_server_name()
include_once('fonctions.php');

//le logo
function logo ()
{
  if(defined('LOGOBANDEAU')) {
    echo '<div class="logo"><img src="./'. LOGOBANDEAU .'" height="74" alt="logo"></div>'."\n";
  }
}


#le bandeau principal
function bandeau_tete()
{
  echo '<div class="bandeau">'.NOMAPPLICATION.'</div>'."\n";
}


// bandeaux de titre
function bandeau_titre($titre)
{
  echo '<div class="bandeautitre">'. $titre .'</div>'."\n";
}


function liste_lang()
{
  global $ALLOWED_LANGUAGES;
  
  $str = '';
  foreach ($ALLOWED_LANGUAGES as $k => $v ) {
    $str .= '<a href="' . $_SERVER['PHP_SELF'] . '?lang=' . $k . '">' . $v . '</a>' . "\n" ;
  }
  
  return $str;
}


#Les sous-bandeaux contenant les boutons de navigation
function sous_bandeau()
{
  echo '<div class="sousbandeau">' .
       '<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>' .
       '<a href="' . get_server_name() . 'studs.php?sondage=aqg259dth55iuhwm">'. _("Example") .'</a>' .
       '<a href="' . get_server_name() . 'contacts.php">'. _("Contact") .'</a>' .
       //'<a href="' . get_server_name() . 'sources/sources.php">'. _("Sources") .'</a>' . //not implemented
       '<a href="' . get_server_name() . 'apropos.php">'. _("About") .'</a>' .
       '<a href="' . get_server_name() . 'admin/index.php">'. _("Admin") .'</a>' .
       '<span class="sousbandeau sousbandeaulangue">' .
       liste_lang() . '</span>'.
       '</div>' . "\n";
}


function sous_bandeau_admin()
{
  echo '<div class="sousbandeau">' .
       '<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>';
  
  if(is_readable('logs_studs.txt')) {
    echo '<a href="' . get_server_name() . 'logs_studs.txt">'. _("Logs") .'</a>';
  }
  
  echo '<a href="' . get_server_name() . '../scripts/nettoyage_sondage.php">'. _("Cleaning") .'</a>' .
       '<span class="sousbandeau sousbandeaulangue">' .
       liste_lang() . '</span>'.
       '</div>'."\n";
}


function sous_bandeau_choix()
{
  echo '<div class="sousbandeau">' .
       '<a href="' . get_server_name() . 'index.php">'. _("Home") .'</a>' .
       '</div>'."\n";
}


#les bandeaux de pied
function sur_bandeau_pied()
{
  echo '<div class="surbandeaupied"></div>'."\n";
}


function bandeau_pied()
{
  echo '<div class="bandeaupied">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";
}


function bandeau_pied_mobile()
{
  echo '<div class="surbandeaupiedmobile"></div>'."\n" .
       '<div class="bandeaupiedmobile">'. _("Universit&eacute; de Strasbourg. Creation: Guilhem BORGHESI. 2008-2009") .'</div>'."\n";
}