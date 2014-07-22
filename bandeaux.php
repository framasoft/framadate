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

include_once __DIR__ . '/app/inc/functions.php';

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
function sous_bandeau_admin()
{
    echo '
    <div class="sousbandeau">
        <ul>
            <li><a class="button small gray" href="'.str_replace('/admin','',get_server_name()).'">'. _("Home") .'</a></li>';

    if (is_readable('logs_studs.txt')) {
        echo '
            <li><a role="button" class="button small gray" href="'.str_replace('/admin','',get_server_name()).'admin/logs_studs.txt">'. _("Logs") .'</a></li>';
    }

    echo '
            <li><a role="button" class="button small gray" href="'.str_replace('/admin','',get_server_name()).'scripts/nettoyage_sondage.php">'. _("Cleaning") .'</a></li>
        </ul>
        <ul class="sousbandeau sousbandeaulangue">
            <li><form method="post" action="">
                <select name="lang" title="'. _("Change the language") .'" class="small white" >' . liste_lang() . '</select>
                <input type="submit" value="OK" class="small white" />
            </form></li>
        </ul>'.
    '</div>'."\n";
}

function bandeau_pied()
{
    echo '
    <div class="sousbandeau">
        <ul>
            <li><a class="button small gray" href="'.get_server_name().'">'. _("Home") .'</a></li>
            <li><a class="button small gray" href="' . getUrlSondage('aqg259dth55iuhwm').'">'. _("Example") .'</a></li>
            <li><a class="button small gray" href="http://contact.framasoft.org">'. _("Contact") .'</a></li>
            <li><a class="button small gray" href="'.get_server_name().'apropos.php">'. _("About") .'</a></li>
        </ul>
        <ul class="sousbandeau sousbandeaulangue">
            <li><form method="post" action="">
                <select name="lang" title="'. _("Change the language") .'" class="small white" >' . liste_lang() . '</select>
                <input type="submit" value="OK" class="small white" />
            </form></li>
        </ul>
    </div>'."\n";
}
