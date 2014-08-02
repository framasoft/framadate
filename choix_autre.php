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
 namespace Framadate;

session_start();
include_once('creation_sondage.php');

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

//si les variables de session ne sont pas valides, il y a une erreur
if (Utils::issetAndNoEmpty('titre', $_SESSION) === false || Utils::issetAndNoEmpty('nom', $_SESSION) === false || Utils::issetAndNoEmpty('adresse', $_SESSION) === false) {

    Utils::print_header(_("Poll subjects (2 on 2)"));

    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alert-danger">
        <h2>' . _("You haven't filled the first section of the poll creation.") . ' !</h2>
        <p>' . _("Back to the homepage of ") . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    //bandeau de pied
    bandeau_pied();

} else {
    //partie creation du sondage dans la base SQL
    //On prépare les données pour les inserer dans la base

    $erreur = false;
    $testdate = true;
    $date_selected = '';

    if (isset($_POST["confirmecreation"])) {
        //recuperation des données de champs textes
        $toutchoix = '';
        for ($i = 0; $i < $_SESSION["nbrecases"] + 1; $i++) {
            if (isset($_POST["choix"]) && Utils::issetAndNoEmpty($i, $_POST["choix"])) {
                $toutchoix.=',';
                $toutchoix.=str_replace(",", " ", htmlentities(html_entity_decode($_POST["choix"][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
            }
        }

        $toutchoix=substr("$toutchoix",1);
        $_SESSION["toutchoix"]=$toutchoix;

        if (Utils::issetAndNoEmpty('champdatefin')) {
            $registredate=explode("/",$_POST["champdatefin"]);
            if (is_array($registredate) === false || count($registredate) !== 3) {
                $testdate = false;
                $date_selected = $_POST["champdatefin"];
            } else {
                $time = mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                if ($time === false || date('d/m/Y', $time) !== $_POST["champdatefin"]) {
                    $testdate = false;
                    $date_selected = $_POST["champdatefin"];
                } else {
                    if (mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]) > time() + 250000) {
                        $_SESSION["champdatefin"]=mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                    }
                }
            }
        } else {
            $_SESSION["champdatefin"]=time()+15552000;
        }

        if ($testdate === true) {
            //format du sondage AUTRE
            $_SESSION["formatsondage"]="A".$_SESSION["studsplus"];

            ajouter_sondage();
        } else {
            $_POST["fin_sondage_autre"] = 'ok';
        }
    }

    // recuperation des sujets pour sondage AUTRE
    $erreur_injection = false;
    if (isset($_SESSION["nbrecases"])) {
        for ($i = 0; $i < $_SESSION["nbrecases"]; $i++) {
            if (isset($_POST["choix"]) && isset($_POST["choix"][$i])) {
                $_SESSION["choix$i"]=htmlentities(html_entity_decode($_POST["choix"][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            }
        }
    } else { //nombre de cases par défaut
        $_SESSION["nbrecases"]=10;
    }

    if (isset($_POST["ajoutcases"])) {
        $_SESSION["nbrecases"]=$_SESSION["nbrecases"]+5;
    }

    //test de remplissage des cases
    $testremplissage = '';
    for ($i=0;$i<$_SESSION["nbrecases"];$i++) {
        if (isset($_POST["choix"]) && Utils::issetAndNoEmpty($i, $_POST["choix"])) {
            $testremplissage="ok";
        }
    }

    if( ($testremplissage != "ok" && (isset($_POST["fin_sondage_autre"]))) || ($testdate === false) || ($erreur_injection) ) {
        // S'il y a des erreurs
        Utils::print_header( _("Error!") .' - '. _("Poll subjects (2 on 2)"));
    } else {
        Utils::print_header( _("Poll subjects (2 on 2)"));
    }

    bandeau_titre(_("Poll subjects (2 on 2)"));

    echo '
    <form name="formulaire" action="#bas" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">

            <div class="alert alert-info">
                <p>'. _("Your poll aim is to make a choice between different subjects.") . '<br />
                ' . _("Enter the subjects to vote for:") .'</p>
            </div>'."\n";

    //message d'erreur si aucun champ renseigné
    if ($testremplissage != "ok" && (isset($_POST["fin_sondage_autre"]))) {
        echo '<div class="alert alert-danger"><p>' . _("Enter at least one choice") . '</p></div>';
        $erreur = true;
    }

    //message d'erreur si mauvaise date
    if ($testdate === false) {
        echo '<div class="alert alert-danger"><p>' . _("Date must be have the format dd/mm/yyyy") . '</p></div>';
    }

    if ($erreur_injection) {
        echo '<div class="alert alert-danger"><p>' . _("Characters \" < and > are not permitted") . '</p></div>';
    }

    //affichage des cases texte de formulaire
    for ($i = 0; $i < $_SESSION["nbrecases"]; $i++) {
        $j = $i + 1;
        if (isset($_SESSION["choix$i"]) === false) {
            $_SESSION["choix$i"] = '';
        }
        echo '
            <div class="form-group">
                <label for="choix'.$i.'" class="col-sm-2 control-label">'. _("Choice") .' '.$j.'</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="choix[]" size="40" maxlength="40" value="'.str_replace("\\","",$_SESSION["choix$i"]).'" id="choix'.$i.'" />
                </div>
            </div>'."\n";
    }

    //focus javascript sur premiere case
    echo '<script type="text/javascript"> document.formulaire.choix0.focus(); </script>'."\n";

    //ajout de cases supplementaires
    echo '<p>'. _("5 choices more") .' <button class="btn btn-link" type="submit" title="'. _("5 choices more").'" name="ajoutcases"><span class="glyphicon glyphicon-plus text-success"></span></button></p>'."\n";

    if (!isset($_POST["fin_sondage_autre"])) {
        echo '<p class="text-right"><button name="fin_sondage_autre" value="'._('Next').'" type="submit" class="btn btn-success">'. _('Next') . '</button></p>';
    }

    if ((isset($_POST["fin_sondage_autre"])) && !$erreur && !$erreur_injection) {
        //demande de la date de fin du sondage
        echo '
    <div class="alert alert-info">
        <p>' . _("Your poll will be automatically removed after 6 months.") . '<br />' . _("You can fix another removal date for it.") .'</p>
        <div class="form-group">
            <label for="champdatefin" class="col-sm-5 control-label">'. _("Removal date (optional)") .'</label>
            <div class="col-sm-6">
                <div class="input-group date">
                    <input type="text" class="form-control" id="champdatefin" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat" name="champdatefin" value="'.$date_selected.'" size="10" maxlength="10" placeholder="'. _("dd/mm/yyyy") .'" /><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                </div>
            </div>
            <span id="dateformat" class="sr-only">'. _("(DD/MM/YYYY)") .'</span>
        </div>
    </div>
    <div class="alert alert-warning">
        <p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll.").'</p>
        <p>'. _("Then, you will receive quickly an email contening the link to your poll for sending it to the voters.").'</p>
    </div>

    <p class="text-right"><button name="confirmecreation" value="confirmecreation" type="submit" class="btn btn-success">'. _('Make a poll') . '</button></p>'."\n";

    }

    //fin du formulaire et bandeau de pied
    echo '</div>
    </div>
    </form>
        <a id="bas"></a>'."\n";

    //bandeau de pied
    bandeau_pied();
}
