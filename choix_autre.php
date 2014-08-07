<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate;

session_start();
include_once('creation_sondage.php');

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Step 1/3 : error if $_SESSION from info_sondage are not valid
if (Utils::issetAndNoEmpty('titre', $_SESSION) === false || Utils::issetAndNoEmpty('nom', $_SESSION) === false || Utils::issetAndNoEmpty('adresse', $_SESSION) === false) {

    Utils::print_header ( _("Error!") );
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alert-danger">
        <h2>' . _("You haven't filled the first section of the poll creation.") . ' !</h2>
        <p>' . _("Back to the homepage of ") . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    bandeau_pied();

} else {
    // Step 4 : Data prepare before insert in DB
    if (isset($_POST["confirmecreation"])) {
        //recuperation des données de champs textes
        $temp_results = '';
        if (isset($_SESSION['choices'])) {
            for ($i = 0; $i < count($_SESSION['choices']); $i++) {
                if ($_SESSION['choices'][$i]!="") {
                    $temp_results.=','.str_replace(",", " ", htmlentities(html_entity_decode($_SESSION['choices'][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
                }
            }
        }

        $temp_results=substr($temp_results,1);
        $_SESSION["toutchoix"]=$temp_results;

        // Expiration date → after 6 months if not filled or in bad format
        $_SESSION["champdatefin"]=time()+15552000;

        if (Utils::issetAndNoEmpty('champdatefin')) {
            $registredate = explode("/",$_POST["champdatefin"]);
            if (is_array($registredate) == true && count($registredate) == 3) {
                $time = mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                if ($time > time() + 250000) {
                    $_SESSION["champdatefin"]=mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                }
            }
        }

        //format du sondage AUTRE
        $_SESSION["formatsondage"]="A".$_SESSION["studsplus"];

        unset($_SESSION['choices']); // session_unset() may not work in ajouter_sondage()
        ajouter_sondage();

    }

    // recuperation des sujets pour sondage AUTRE
    if (isset($_POST['choices'])) {
        for ($i = 0; $i < count($_POST['choices']); $i++) {
            if (Utils::issetAndNoEmpty($i, $_POST['choices'])) {
                $_SESSION['choices'][$i]=htmlentities(html_entity_decode($_POST['choices'][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
            }
        }
    }


    Utils::print_header( _("Poll subjects (2 on 2)"));
    bandeau_titre(_("Poll subjects (2 on 2)"));

    echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_autre.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">';

    // Step 3/3 : Confirm poll creation and choose a removal date
    if (isset($_POST["fin_sondage_autre"])) {
        //demande de la date de fin du sondage
        echo '
            <div class="alert alert-info">
                <p>' . _("Your poll will be automatically removed after 6 months.") . '<br />' . _("You can fix another removal date for it.") .'</p>
                <div class="form-group">
                    <label for="champdatefin" class="col-sm-5 control-label">'. _("Removal date (optional)") .'</label>
                    <div class="col-sm-6">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="champdatefin" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat" name="champdatefin" value="" size="10" maxlength="10" placeholder="'. _("dd/mm/yyyy") .'" />
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

    // Step 2/3 : Select choices of the poll
    } else {
        echo '
            <div class="alert alert-info">
                <p>'. _("Your poll aim is to make a choice between different subjects.") . '<br />
                ' . _("Enter the subjects to vote for:") .'</p>
            </div>'."\n";

        // Fields choices : 5 by default
        $nb_choices = (isset($_SESSION['choices'])) ? max(count($_SESSION['choices']), 5) : 5;
        for ($i = 0; $i < $nb_choices; $i++) {
            $choice_value = (isset($_SESSION['choices'][$i])) ? str_replace("\\","",$_SESSION['choices'][$i]) : '';
            echo '
            <div class="form-group choice-field">
                <label for="choice'.$i.'" class="col-sm-2 control-label">'. _("Choice") .' '.($i+1).'</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="choices[]" size="40" maxlength="40" value="'.$choice_value.'" id="choice'.$i.'" />
                </div>
            </div>'."\n";
        }

        echo '
            <div class="col-md-6">
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-choice" class="btn btn-default" title="'. _("Remove a choice") .'"><span class="glyphicon glyphicon-minus text-info"></span></button>
                    <button type="button" id="add-a-choice" class="btn btn-default" title="'. _("Add a choice") .'"><span class="glyphicon glyphicon-plus text-success"></span></button>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <button name="fin_sondage_autre" value="'._('Next').'" type="submit" class="btn btn-success disabled">'. _('Next') . '</button>
            </div>'."\n";
    }

    echo '
        </div>
    </div>
    </form>'."\n";

    bandeau_pied();
}
