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
if (Utils::issetAndNoEmpty('titre', $_SESSION) === false || Utils::issetAndNoEmpty('nom', $_SESSION) === false || (($config['use_smtp']) ? Utils::issetAndNoEmpty('adresse', $_SESSION) === false : false)) {

    Utils::print_header ( _("Error!") );
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alter-danger">
        <h3>' . _("You haven't filled the first section of the poll creation.") . ' !</h3>
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
                if(count($_SESSION['horaires'.$i])!=0) {
                    for ($j=0;$j< min(count($_SESSION['horaires'.$i]),12);$j++) {
                        if ($_SESSION['horaires'.$i][$j]!="") {
                            array_push($temp_results, $_SESSION["totalchoixjour"][$i].'@'.$_SESSION['horaires'.$i][$j]);
                        } else {
                            array_push($temp_results, $_SESSION["totalchoixjour"][$i]);
                        }
                    }
                } else {
                    array_push($temp_results, $_SESSION["totalchoixjour"][$i]);
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

        // Expiration date → 6 months after last day if not filled or in bad format
        $_SESSION["champdatefin"]=end($temp_results)+(86400 * $config['default_poll_duration']);

        if (Utils::issetAndNoEmpty('champdatefin')) {
            $registredate = explode("/",$_POST["champdatefin"]);
            if (is_array($registredate) == true && count($registredate) == 3) {
                $time = mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                if ($time > time() + (24*60*60)) {
                    $_SESSION["champdatefin"]=$time;
                }
            }
        }

        ajouter_sondage();

    } else {
        if (Utils::issetAndNoEmpty('days')) {
            if (!isset($_SESSION["totalchoixjour"])) {
              $_SESSION["totalchoixjour"]=array();
            }
            $k = 0;
            for ($i = 0; $i < count($_POST["days"]); $i++) {
                if (isset($_POST["days"][$i]) && $_POST["days"][$i] !='') {
                    $_SESSION['totalchoixjour'][$k] = mktime(0, 0, 0, substr($_POST["days"][$i],3,2),substr($_POST["days"][$i],0,2),substr($_POST["days"][$i],6,4));

                    $l = 0;
                    for($j = 0; $j < count($_POST['horaires'.$i]); $j++) {
                        if (isset($_POST['horaires'.$i][$j]) && $_POST['horaires'.$i][$j] != '') {
                            $_SESSION['horaires'.$k][$l] = $_POST['horaires'.$i][$j];
                            $l++;
                        }
                    }
                    $k++;
                }
            }
        }
    }

    //le format du sondage est DATE
    $_SESSION["formatsondage"] = "D".$_SESSION["studsplus"];

    // Step 3/3 : Confirm poll creation
    if (Utils::issetAndNoEmpty('choixheures') && Utils::issetAndNoEmpty('totalchoixjour', $_SESSION)) {

        Utils::print_header ( _("Removal date and confirmation (3 on 3)") );
        bandeau_titre(_("Removal date and confirmation (3 on 3)"));

        $temp_array = array_unique($_SESSION["totalchoixjour"]);
        sort($temp_array);
        $removal_date=utf8_encode(strftime($date_format['txt_full'], end($temp_array)+ (86400 * $config['default_poll_duration'])));

        // Sumary
        $summary = '<ul>';
        for ($i=0;$i<count($_SESSION["totalchoixjour"]);$i++) {
            $summary .= '<li>'.strftime($date_format['txt_full'], $_SESSION["totalchoixjour"][$i]);
            for ($j=0;$j<count($_SESSION['horaires'.$i]);$j++) {
                if (isset($_SESSION['horaires'.$i][$j])) {
                    $summary .= ($j==0) ? ' : ' : ', ';
                    $summary .= $_SESSION['horaires'.$i][$j];
                }
            }
            $summary .= '</li>'."\n";
        }
        $summary .= '</ul>';

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
        <div class="col-md-8 col-md-offset-2">
            <h3>'. _("Confirm the creation of your poll") .'</h3>
            <div class="well summary">
                <h4>'. _("List of your choices").'</h4>
                '. $summary .'
            </div>
            <div class="alert alert-info clearfix">
                <p>' . _("Your poll will be automatically removed "). $config['default_poll_duration'] . ' ' . _("days") ._(" after the last date of your poll:") . ' <strong>'.$removal_date.'</strong>.<br />' . _("You can fix another removal date for it.") .'</p>
                <div class="form-group">
                    <label for="champdatefin" class="col-sm-5 control-label">'. _("Removal date (optional)") .'</label>
                    <div class="col-sm-6">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="champdatefin" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat" name="champdatefin" value="" size="10" maxlength="10" placeholder="'. _("dd/mm/yyyy") .'" />
                        </div>
                    </div>
                    <span id="dateformat" class="sr-only">'. _("(dd/mm/yyyy)") .'</span>
                </div>
            </div>
            <div class="alert alert-warning">
                <p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll."). '</p>';
        if($config['use_smtp']==true){
            echo '
                <p>' . _("Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.") .'</p>';
        }
        echo '
            </div>
            <p class="text-right">
                <button class="btn btn-default" onclick="javascript:window.history.back();" title="'. _('Back to step 2') . '">'. _('Back') . '</button>
                <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">'. _('Create the poll') . '</button>
            </p>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    // Step 2/3 : Select dates of the poll
    } else {
        Utils::print_header ( _("Poll dates (2 on 3)") );
        bandeau_titre(_("Poll dates (2 on 3)"));

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row" id="selected-days">
        <div class="col-md-10 col-md-offset-1">
            <h3>'. _("Choose the dates of your poll") .'</h3>
            <div class="alert alert-info">
                <p>'. _("To schedule an event, it's better to propose at least two choices (two hours for one day or two days).").'</p>
                <p>'. _("You can add or remove additionnal days and hours with the buttons") .' <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. _("Remove") .'</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add") .'</span></p>
                <p>'. _("For each selected day, you can choose, or not, meeting hours (e.g.: \"8h\", \"8:30\", \"8h-10h\", \"evening\", etc.)").'</p>
            </div>';

        // Fields days : 3 by default
        $nb_days = (isset($_SESSION["totalchoixjour"])) ? count($_SESSION["totalchoixjour"]) : 3;
        for ($i=0;$i<$nb_days;$i++) {
            $day_value = isset($_SESSION["totalchoixjour"][$i]) ? strftime( "%d/%m/%Y", $_SESSION["totalchoixjour"][$i]) : '';
            echo '
            <fieldset>
                <div class="form-group">
                    <legend>
                        <label class="sr-only" for="day'.$i.'">'. _("Day") .' '. ($i+1) .'</label>
                        <div class="input-group date col-xs-7">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="day'.$i.'" title="'. _("Day") .' '. ($i+1) .'" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat'.$i.'" name="days[]" value="'.$day_value.'" size="10" maxlength="10" placeholder="'. _("dd/mm/yyyy") .'" />
                        </div>
                        <span id="dateformat'.$i.'" class="sr-only">'. _("(dd/mm/yyyy)") .'</span>
                    </legend>'."\n";

            // Fields hours : 3 by default
            for ($j=0;$j<max(count(isset($_SESSION["horaires".$i]) ? $_SESSION["horaires".$i] : 0),3);$j++) {
                $hour_value = isset($_SESSION["horaires".$i][$j]) ? $_SESSION["horaires".$i][$j] : '';
                echo '
                    <div class="col-sm-2">
                        <label for="d'.$i.'-h'.$j.'" class="sr-only control-label">'. _("Time") .' '. ($j+1) .'</label>
                        <input type="text" class="form-control hours" title="'.$day_value.' - '. _("Time") .' '. ($j+1) .'" placeholder="'. _("Time") .' '. ($j+1) .'" id="d'.$i.'-h'.$j.'" name="horaires'.$i.'[]" value="'.$hour_value.'" />
                    </div>'."\n";
            }
            echo '
                    <div class="col-sm-2"><div class="btn-group btn-group-xs" style="margin-top: 5px;">
                        <button type="button" title="'. _("Remove an hour") .'" class="remove-an-hour btn btn-default"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. _("Remove an hour") .'</span></button>
                        <button type="button" title="'. _("Add an hour") .'" class="add-an-hour btn btn-default"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add an hour") .'</span></button>
                    </div></div>
                </div>
            </fieldset>';
            }
        echo '
            <div class="col-md-4">
                <button type="button" id="copyhours" class="btn btn-default disabled" title="'. _("Copy hours of the first day") .'"><span class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span><span class="sr-only">'. _("Copy hours of the first day") .'</span></button>
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-day" class="btn btn-default disabled" title="'. _("Remove a day") .'"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. _("Remove a day") .'</span></button>
                    <button type="button" id="add-a-day" class="btn btn-default" title="'. _("Add a day") .'"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add a day") .'</span></button>
                </div>
            </div>
            <div class="col-md-8 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-remove text-danger"></span> '. _("Remove") . ' <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a id="resetdays" href="javascript:void(0)">'. _("Remove all days") .'</a></li>
                        <li><a id="resethours" href="javascript:void(0)">'. _("Remove all hours") .'</a></li>
                    </ul>
                </div>
                <a class="btn btn-default" href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=date" title="'. _('Back to step 1') . '">'. _('Back') . '</a>
                <button name="choixheures" value="'. _("Next") .'" type="submit" class="btn btn-success disabled" title="'. _('Go to step 3') . '">'. _("Next") .'</button>
            </div>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    }
}
