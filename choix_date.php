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

if (is_readable('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Step 1/3 : error if $_SESSION from info_sondage are not valid
if (!Utils::issetAndNoEmpty('nom', $_SESSION) && !Utils::issetAndNoEmpty('adresse', $_SESSION) && !Utils::issetAndNoEmpty('commentaires', $_SESSION) && !Utils::issetAndNoEmpty('mail', $_SESSION)) {

    Utils::print_header ( _("Error!") );
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alter-danger">
        <h2>' . _("You haven't filled the first section of the poll creation.") . ' !</h2>
        <p>' . _("Back to the homepage of ") . ' ' . '<a href="' . Utils::get_server_name() . '">' . NOMAPPLICATION . '</a>.</p>
    </div>';

    bandeau_pied();

} else {
    // Step 4 : Data prepare before insert in DB
    if (Utils::issetAndNoEmpty('confirmation')) {
        $temp_results = array();
        $choixdate='';
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                for ($j=0;$j< min(count($_SESSION['horaires'.$i]),12);$j++) {
                    if ($_SESSION['horaires'.$i][$j]!="") {
                        array_push($temp_results, $_SESSION["totalchoixjour"][$i].'@'.$_SESSION['horaires'.$i][$j]);
                    } else {
                        array_push($temp_results, $_SESSION["totalchoixjour"][$i]);
                    }
                }
            }
        }

        // Sort and remove doublons
        $temp_results = array_unique($temp_results);
        sort($temp_results);
        for ($i=0;$i<count($temp_results);$i++) {
            if (isset($temp_results[$i])) {
                $choixdate.=','.$temp_results[$i];
            }
        }

        $_SESSION["toutchoix"]=substr($choixdate,1);

        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            unset($_SESSION['horaires'.$i]); // session_unset() may not work in ajouter_sondage()
        }
        unset($_SESSION["totalchoixjour"]); // session_unset() may not work in ajouter_sondage()

        ajouter_sondage();

    } else {

        if (Utils::issetAndNoEmpty('days')) {
            if (!isset($_SESSION["totalchoixjour"])) {
              $_SESSION["totalchoixjour"]=array();
            }
            for ($i = 0; $i < count($_POST["days"]); $i++) {
                $_SESSION['totalchoixjour'][$i] = mktime(0, 0, 0, substr($_POST["days"][$i],3,2),substr($_POST["days"][$i],0,2),substr($_POST["days"][$i],6,4));

                for($j = 0; $j < count($_POST['horaires'.$i]); $j++) {
                    if ($_POST['horaires'.$i][$j]!="") {
                        $_SESSION['horaires'.$i][$j] = $_POST['horaires'.$i][$j];
                    }
                }
            }
        }
    }

    //le format du sondage est DATE
    $_SESSION["formatsondage"] = "D".$_SESSION["studsplus"];

    //debut de la page web
    Utils::print_header ( _("Poll dates (2 on 2)") );
    bandeau_titre(_("Poll dates (2 on 2)"));

    echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
       <div class="col-md-8 col-md-offset-2">'."\n";

    // Step 3/3 : Confirm poll creation
    if (Utils::issetAndNoEmpty('choixheures') && Utils::issetAndNoEmpty('totalchoixjour', $_SESSION)) {
        $temp_array = array_unique($_SESSION["totalchoixjour"]);
        sort($temp_array);
        $jour_arret = end($temp_array)+2592000;
        $date_fin=strftime(_("%A, den %e. %B %Y"), $jour_arret);
        echo '
            <div class="alert alert-info">
                <p>'. _("Your poll will expire automatically 2 days after the last date of your poll.") .'</p>
                <p>'. _("Removal date") .' : <b> '.$date_fin.'</b></p>
            </div>
            <div class="alert alert-warning">
                <p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll."). '</p>
                <p>' . _("Then, you will receive quickly an email contening the link to your poll for sending it to the voters.") .'</p>
            </div>
            <p class="text-right">
                <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">'. _('Create the poll') . '</button>
            </p>'."\n";

    // Step 2/3 : Select dates of the poll
    } else {
        echo '
            <h2>'. _("Selected days") .' :</h2>
            <div class="alert alert-info">
                <p>'._("For each selected day, you can choose, or not, meeting hours (e.g.: \"8h\", \"8:30\", \"8h-10h\", \"evening\", etc.)") .'</p>
            </div>';

            // Fields days : 1 by default
            $nb_days = (isset($_SESSION["totalchoixjour"])) ? count($_SESSION["totalchoixjour"]) : 1;
            for ($i=0;$i<$nb_days;$i++) {
                $day_value = isset($_SESSION["totalchoixjour"][$i]) ? strftime( "%d/%m/%Y", $_SESSION["totalchoixjour"][$i]) : '';
                echo '
            <fieldset>
                <div class="form-group">
                    <legend>
                        <div class="input-group date col-xs-7">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="day'.$i.'" data-date-format="'. _("dd/mm/yyyy") .'" name="days[]" value="'.$day_value.'" size="10" maxlength="10" placeholder="'. _("dd/mm/yyyy") .'" />
                        </div>
                    </legend>'."\n";

                // Fields hours : 3 by default
                for ($j=0;$j<max(count($_SESSION['horaires'.$i]),3);$j++) {
                    $hour_value = isset($_SESSION['horaires'.$i][$j]) ? $_SESSION['horaires'.$i][$j] : '';
                    echo '
                    <div class="col-sm-2">
                        <label for="d'.$i.'-h'.$j.'" class="sr-only control-label">'. _("Time") .' '. ($j+1) .'</label>
                        <input type="text" class="form-control hours" title="'.$day_value.' - '. _("Time") .' '. ($j+1) .'" placeholder="'. _("Time") .' '. ($j+1) .'" maxlength="11" id="d'.$i.'-h'.$j.'" name="horaires'.$i.'[]" value="'.$hour_value.'" />
                    </div>'."\n";
                }
                echo '
                    <div class="col-sm-2"><div class="btn-group btn-group-xs" style="margin-top: 5px;">
                        <button type="button" title="'. _("Remove an hour") .'" class="remove-an-hour btn btn-default"><span class="glyphicon glyphicon-minus text-info"></span></button>
                        <button type="button" title="'. _("Add an hour") .'" class="add-an-hour btn btn-default"><span class="glyphicon glyphicon-plus text-success"></span></button>
                    </div></div>
                </div>
            </fieldset>';
            }
            echo '
            <div class="col-md-6">
                <button type="button" id="copyhours" class="btn btn-default disabled" title="'. _("Copy hours of the first day") .'"><span class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span></button>
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-day" class="btn btn-default disabled" title="'. _("Remove a day") .'"><span class="glyphicon glyphicon-minus text-info"></span></button>
                    <button type="button" id="add-a-day" class="btn btn-default" title="'. _("Add a day") .'"><span class="glyphicon glyphicon-plus text-success"></span></button>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-remove text-danger"></span> '. _("Remove") . ' <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a id="resetdays" href="javascript:void(0)">'. _("Remove all days") .'</a></li>
                        <li><a id="resethours" href="javascript:void(0)">'. _("Remove all hours") .'</a></li>
                    </ul>
                </div>
                <button name="choixheures" value="'. _("Next") .'" type="submit" class="btn btn-success disabled">'. _('Next') . '</button>
            </div>'."\n";
    }

    echo '
        </div>
    </div>
    </form>'."\n";

    bandeau_pied();

}
