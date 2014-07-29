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
include_once __DIR__ . '/app/inc/functions.php';

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// On teste toutes les variables pour supprimer l'ensemble des warnings PHP
// On transforme en entites html les données afin éviter les failles XSS
$post_var = array('choix_sondage', 'poursuivre', 'titre', 'nom', 'adresse', 'commentaires', 'studsplus', 'mailsonde', 'creation_sondage_date', 'creation_sondage_date_x', 'creation_sondage_autre', 'creation_sondage_autre_x',);
foreach ($post_var as $var) {
    if (isset($_POST[$var]) === true) {
        $$var = htmlentities($_POST[$var], ENT_QUOTES, 'UTF-8');
    } else {
        $$var = null;
    }
}

// On initialise egalement la session car sinon bonjour les warning :-)
$session_var = array('choix_sondage', 'titre', 'nom', 'adresse', 'commentaires', 'mailsonde', 'studsplus', );
foreach ($session_var as $var) {
    if (issetAndNoEmpty($var, $_SESSION) === false) {
        $_SESSION[$var] = null;
    }
}

// On initialise également les autres variables
$erreur_adresse = false;
$erreur_injection_titre = false;
$erreur_injection_nom = false;
$erreur_injection_commentaires = false;
$cocheplus = '';
$cochemail = '';

#tests
if (issetAndNoEmpty("poursuivre")){
    $_SESSION["choix_sondage"] = $choix_sondage;
    $_SESSION["titre"] = $titre;
    $_SESSION["nom"] = $nom;
    $_SESSION["adresse"] = $adresse;
    $_SESSION["commentaires"] = $commentaires;

    unset($_SESSION["studsplus"]);
    $_SESSION["studsplus"] = ($studsplus !== null) ? '+' : $_SESSION["studsplus"] = '';

    unset($_SESSION["mailsonde"]);
    $_SESSION["mailsonde"] = ($mailsonde !== null) ? true : false;
 
    if (validateEmail($adresse) === false) {
        $erreur_adresse = true;
    }

    if (preg_match(';<|>|";',$titre)) {
        $erreur_injection_titre = true;
    }

    if (preg_match(';<|>|";',$nom)) {
        $erreur_injection_nom = true;
    }

    if (preg_match(';<|>|";',$commentaires)) {
        $erreur_injection_commentaires = true;
    }

    // Si pas d'erreur dans l'adresse alors on change de page vers date ou autre
    if ($titre && $nom && $adresse && !$erreur_adresse && ! $erreur_injection_titre && ! $erreur_injection_commentaires && ! $erreur_injection_nom) {

        if ( $poursuivre == "creation_sondage_date" ) {
            header("Location:choix_date.php");
            exit();
        }

        if ( $poursuivre == "creation_sondage_autre" ) {
            header("Location:choix_autre.php");
            exit();
        }

    } else {
	  // Title Erreur !
	  print_header( _("Error!").' - '._("Poll creation (1 on 2)") );
    }
} else {
	// Title OK (formulaire pas encore rempli)
	print_header( _("Poll creation (1 on 2)") );
}

bandeau_titre( _("Poll creation (1 on 2)") );

// premier sondage ? test l'existence des schémas SQL avant d'aller plus loin
if(!check_table_sondage()) {
    echo '<p calss="error txt-center">' . _("STUdS is not properly installed, please check the 'INSTALL' to setup the database before continuing") . "</p>"."\n";
  
    bandeau_pied();

    die();
}

//debut du formulaire
echo '
    <form name="formulaire" id="formulaire" action="'.get_server_name().'infos_sondage.php" method="POST">
        <p>'. _("You are in the poll creation section.").' <br /> '._("Required fields cannot be left blank.") .'</p>';

//Affichage des différents champs textes a remplir
echo '
        <table role="presentation">
            <tr><td><label for="poll_title">' . _("Poll title *: ") . '</label></td>
            <td><input id="poll_title" type="text" name="titre" size="40" maxlength="80" value="'.stripslashes($_SESSION["titre"]).'"';
if (!$_SESSION["titre"] && issetAndNoEmpty("poursuivre") ) {
  // fermeture de la ligne du dessus avec attribut aria-describeby pour avoir les infos concernant l'erreur
  // pas très propre mais bon...
    echo 'aria-describeby="#poll_title_error"></td>
            <td class="error" id="poll_title_error">' . _("Enter a title") . '</td>';
} elseif ($erreur_injection_titre) {
  // idem
    echo 'aria-describeby="#poll_title_error"></td>
            <td class="error" id="poll_title_error">' . _("Characters < > and \" are not permitted") . '</td>';
} else {
  // pas d'erreur, pas d'aria
  echo '></td>';
}

echo '
            </tr><tr>
            <td><label for="poll_comments">'. _("Description: ") .'</label></td><td><textarea id="poll_comments" name="commentaires" rows="7" cols="40"';

if ($erreur_injection_commentaires) {
    // même principe
    echo 'aria-describeby="#poll_comment_error">'.stripslashes($_SESSION["commentaires"]).'</textarea></td>
            <td class="error" id="poll_comment_error">' . _("Characters < > and \" are not permitted") . "</td>";
} else {
    // pas d'erreur, pas d'aria
    echo '>'.stripslashes($_SESSION["commentaires"]).'</textarea></td>';
}

echo '
            </tr><tr>
            <td><label for="yourname">'. _("Your name*: ") .'</label></td>';

if (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) {
    echo '<td><input type="hidden" name="nom" size="40" maxlength="40" value="'.$_SESSION["nom"].'">'.stripslashes($_SESSION["nom"]).'</td>'."\n";
} else {
    echo '<td><input id="yourname" type="text" name="nom" size="40" maxlength="40" value="'.stripslashes($_SESSION["nom"]).'"></td>'."\n";
}

if (!$_SESSION["nom"] && issetAndNoEmpty("poursuivre")) {
    echo '<td class="error">' . _("Enter a name") . '</td>'."\n";
} elseif ($erreur_injection_nom) {
    echo '<td class="error">' . _("Characters < > and \" are not permitted") . '</td>'."\n";
}

echo '
            </tr><tr>
            <td><label for="email">'. _("Your e-mail address *: ") .'</label></td>';

if (USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) {
    echo '<td><input type="hidden" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'">'.$_SESSION["adresse"].'</td>'."\n";
} else {
    echo '<td><input id="email" type="text" name="adresse" size="40" maxlength="64" value="'.$_SESSION["adresse"].'"></td>'."\n";
}

if (!$_SESSION["adresse"] && issetAndNoEmpty("poursuivre")) {
    echo '<td class="error">' . _("Enter an email address") . ' </td>'."\n";
} elseif ($erreur_adresse && issetAndNoEmpty("poursuivre")) {
    echo '<td class="error">' . _("The address is not correct! (You should enter a valid email address in order to receive the link to your poll)") . '</td>'."\n";
}

echo '
            </tr>
        </table>'."\n";

//focus javascript sur le premier champ
echo '<script type="text/javascript"> document.formulaire.titre.focus(); </script>'."\n";

//ffichage du cochage par défaut
if (!$_SESSION["studsplus"] && !issetAndNoEmpty('creation_sondage_date') && !issetAndNoEmpty('creation_sondage_autre') && !issetAndNoEmpty('creation_sondage_date_x') && !issetAndNoEmpty('creation_sondage_autre_x')) {
    $_SESSION["studsplus"]="+";
}

if ($_SESSION["studsplus"]=="+") {
    $cocheplus="checked";
}

echo '<p><input type=checkbox name=studsplus '.$cocheplus.' id="studsplus"><label for="studsplus"><strong>'. _(" Voters can modify their vote themselves.") .'</strong></label></p>'."\n";

if ($_SESSION["mailsonde"]) {
    $cochemail="checked";
}

echo '<p><input type=checkbox name=mailsonde '.$cochemail.' id="mailsonde"><label for="mailsonde"><strong>'. _(" To receive an email for each new vote.") .'</strong></label></p>'."\n";

//affichage des boutons pour choisir sondage date ou autre
if ($_GET['choix_sondage'] == 'date') {
    $choix = "creation_sondage_date";
} elseif ($_GET['choix_sondage'] == 'autre') {
    $choix = "creation_sondage_autre";
}
echo '
    <p><input type="hidden" name="choix_sondage" value="'. $choix_sondage .'"/>
    <button name="poursuivre" value="'. $choix .'" type="submit" class="button green poursuivre"><strong>'. _('Next') . '</strong> </button></p>

</form>'."\n";

//bandeau de pied
bandeau_pied();
