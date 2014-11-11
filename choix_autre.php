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
        <h3>' . _("You haven't filled the first section of the poll creation.") . ' !</h3>
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

        // Expiration date → the configuration value is used if not filled or in bad format		
        $_SESSION["champdatefin"]= time()+ (86400 * config_get('default_poll_duration')); //60 secondes * 60 minutes * 24 heures * config

        if (Utils::issetAndNoEmpty('champdatefin')) {
            $registredate = explode("/",$_POST["champdatefin"]);
            if (is_array($registredate) == true && count($registredate) == 3) {
                $time = mktime(0,0,0,$registredate[1],$registredate[0],$registredate[2]);
                if ($time > time() + (24*60*60)) {
                    $_SESSION["champdatefin"]=$time;
                }
            }
        }

        //format du sondage AUTRE
        $_SESSION["formatsondage"]="A".$_SESSION["studsplus"];

        ajouter_sondage();

    }

    // recuperation des sujets pour sondage AUTRE
    if (isset($_POST['choices'])) {
        $k = 0;
        for ($i = 0; $i < count($_POST['choices']); $i++) {
            if (Utils::issetAndNoEmpty($i, $_POST['choices'])) {
                $_SESSION['choices'][$k]=htmlentities(html_entity_decode($_POST['choices'][$i], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
                $k++;
            }
        }
    }

    // Step 3/3 : Confirm poll creation and choose a removal date
    if (isset($_POST["fin_sondage_autre"])) {
        Utils::print_header ( _("Removal date and confirmation (3 on 3)") );
        bandeau_titre(_("Removal date and confirmation (3 on 3)"));

        $removal_date=strftime(_("%A, den %e. %B %Y"), time()+15552000);

        // Sumary
        $summary = '<ol>';
        for ($i=0;$i<count($_SESSION['choices']);$i++) {

            preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/',$_SESSION['choices'][$i],$md_a_img);  // Markdown [![alt](src)](href)
            preg_match_all('/!\[(.*?)\]\((.*?)\)/',$_SESSION['choices'][$i],$md_img);                 // Markdown ![alt](src)
            preg_match_all('/\[(.*?)\]\((.*?)\)/',$_SESSION['choices'][$i],$md_a);                    // Markdown [text](href)
            if (isset($md_a_img[2][0]) && $md_a_img[2][0]!='' && isset($md_a_img[3][0]) && $md_a_img[3][0]!='') { // [![alt](src)](href)

                $li_subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0]!='') ? stripslashes($md_a_img[1][0]) : _("Choice") .' '.($i+1);
                $li_subject_html = '<a href="'.$md_a_img[3][0].'"><img src="'.$md_a_img[2][0].'" class="img-responsive" alt="'.$li_subject_text.'" /></a>';

            } elseif (isset($md_img[2][0]) && $md_img[2][0]!='') { // ![alt](src)

                $li_subject_text = (isset($md_img[1][0]) && $md_img[1][0]!='') ? stripslashes($md_img[1][0]) : _("Choice") .' '.($i+1);
                $li_subject_html = '<img src="'.$md_img[2][0].'" class="img-responsive" alt="'.$li_subject_text.'" />';

            } elseif (isset($md_a[2][0]) && $md_a[2][0]!='') { // [text](href)

                $li_subject_text = (isset($md_a[1][0]) && $md_a[1][0]!='') ? stripslashes($md_a[1][0]) : _("Choice") .' '.($i+1);
                $li_subject_html = '<a href="'.$md_a[2][0].'">'.$li_subject_text.'</a>';

            } else { // text only

                $li_subject_text = stripslashes($_SESSION['choices'][$i]);
                $li_subject_html = $li_subject_text;

            }

            $summary .= '<li>'.$li_subject_html.'</li>'."\n";
        }
        $summary .= '</ol>';

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_autre.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="well summary">
                <h4>'. _("List of your choices").'</h4>
                '. $summary .'
            </div>
            <div class="alert alert-info">
                <p>' . _("Your poll will be automatically removed after"). " " . config_get('default_poll_duration') . " " . _("days") . ' <strong>'.$removal_date.'</strong>.<br />' . _("You can fix another removal date for it.") .'</p>
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
                <p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll."). '</p>
                <p>' . _("Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.") .'</p>
            </div>
            <p class="text-right">
                <button class="btn btn-default" onclick="javascript:window.history.back();" title="'. _('Back to step 2') . '">'. _('Back') . '</button>
                <button name="confirmecreation" value="confirmecreation" type="submit" class="btn btn-success">'. _('Create the poll') . '</button>
            </p>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    // Step 2/3 : Select choices of the poll
    } else {
        Utils::print_header( _("Poll subjects (2 on 3)"));
        bandeau_titre(_("Poll subjects (2 on 3)"));

        echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_autre.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">';
        echo '
            <div class="alert alert-info">
                <p>'. _("To make a generic poll you need to propose at least two choices between differents subjects.") .'</p>
                <p>'. _("You can add or remove additional choices with the buttons") .' <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. _("Remove") .'</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add") .'</span></p>
                <p>'. _("It's possible to propose links or images by using "). '<a href="http://'.$lang.'.wikipedia.org/wiki/Markdown">'. _("the Markdown syntax") .'</a>.</p>
            </div>'."\n";

        // Fields choices : 5 by default
        $nb_choices = (isset($_SESSION['choices'])) ? max(count($_SESSION['choices']), 5) : 5;
        for ($i = 0; $i < $nb_choices; $i++) {
            $choice_value = (isset($_SESSION['choices'][$i])) ? str_replace("\\","",$_SESSION['choices'][$i]) : '';
            echo '
            <div class="form-group choice-field">
                <label for="choice'.$i.'" class="col-sm-2 control-label">'. _("Choice") .' '.($i+1).'</label>
                <div class="col-sm-10 input-group">
                    <input type="text" class="form-control" name="choices[]" size="40" value="'.$choice_value.'" id="choice'.$i.'" />
                    <span class="input-group-addon btn-link md-a-img" title="'. _("Add a link or an image") .' - '. _("Choice") .' '.($i+1).'" ><span class="glyphicon glyphicon-picture"></span> <span class="glyphicon glyphicon-link"></span></span>
                </div>
            </div>'."\n";
        }

        echo '
            <div class="col-md-4">
                <div class="btn-group btn-group">
                    <button type="button" id="remove-a-choice" class="btn btn-default" title="'. _("Remove a choice") .'"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">'. _("Remove") .'</span></button>
                    <button type="button" id="add-a-choice" class="btn btn-default" title="'. _("Add a choice") .'"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add") .'</span></button>
                </div>
            </div>
            <div class="col-md-8 text-right">
                <a class="btn btn-default" href="'.Utils::get_server_name().'infos_sondage.php?choix_sondage=autre" title="'. _('Back to step 1') . '">'. _('Back') . '</a>
                <button name="fin_sondage_autre" value="'._('Next').'" type="submit" class="btn btn-success disabled" title="'. _('Go to step 3') . '">'. _('Next') . '</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="md-a-imgModal" tabindex="-1" role="dialog" aria-labelledby="md-a-imgModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">'. _('Close') . '</span></button>
                    <h4 class="modal-title" id="md-a-imgModalLabel">'. _("Add a link or an image") .'</h4>
                </div>
                <div class="modal-body">
                    <p class="alert alert-info">'. _("These fields are optional. You can add a link, an image or both.") .'</p>
                    <div class="form-group">
                        <label for="md-img"><span class="glyphicon glyphicon-picture"></span> '. _('URL of the image') . '</label>
                        <input id="md-img" type="text" placeholder="http://…" class="form-control" size="40" />
                    </div>
                    <div class="form-group">
                        <label for="md-a"><span class="glyphicon glyphicon-link"></span> '. _('Link') . '</label>
                        <input id="md-a" type="text" placeholder="http://…" class="form-control" size="40" />
                    </div>
                    <div class="form-group">
                        <label for="md-text">'. _('Alternative text') . '</label>
                        <input id="md-text" type="text" class="form-control" size="40" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">'. _('Cancel') . '</button>
                    <button type="button" class="btn btn-primary">'. _('Add') . '</button>
                </div>
            </div>
        </div>
    </div>
    </form>'."\n";

        bandeau_pied();

    }
}
