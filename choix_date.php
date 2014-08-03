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

//si les variables de session ne snot pas valides, il y a une erreur
if (!Utils::issetAndNoEmpty('nom', $_SESSION) && !Utils::issetAndNoEmpty('adresse', $_SESSION) && !Utils::issetAndNoEmpty('commentaires', $_SESSION) && !Utils::issetAndNoEmpty('mail', $_SESSION)) {

    Utils::print_header ( _("Error!") );
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alter-danger">
        <h2>' . _("You haven't filled the first section of the poll creation.") . ' !</h2>
        <p>' . _("Back to the homepage of ") . ' ' . '<a href="' . Utils::get_server_name() . '">' . NOMAPPLICATION . '</a>.</p>
    </div>';

    //bandeau de pied
    bandeau_pied();

} else { //sinon on peut afficher le calendrier normalement
    //partie creation du sondage dans la base SQL
    //On prépare les données pour les inserer dans la base
    if (Utils::issetAndNoEmpty('confirmation')) {
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                if ($_SESSION["horaires$i"][0] == "" && $_SESSION["horaires$i"][1] == "" && $_SESSION["horaires$i"][2] == "" && $_SESSION["horaires$i"][3] == "" && $_SESSION["horaires$i"][4] == "") {
                    $choixdate.=",";
                    $choixdate .= $_SESSION["totalchoixjour"][$i];
                } else {
                    for ($j=0;$j<$_SESSION["nbrecaseshoraires"];$j++) {
                        if ($_SESSION["horaires$i"][$j]!="") {
                            $choixdate.=",";
                            $choixdate .= $_SESSION["totalchoixjour"][$i];
                            $choixdate.="@";
                            // On remplace la virgule et l'arobase pour ne pas avoir de problème par la suite
                            $choixdate .= str_replace(array(',', '@'), array('&#44;', '&#64;'), $_SESSION["horaires$i"][$j]);
                        }
                    }
                }
            }
        }

        $_SESSION["toutchoix"]=substr("$choixdate",1);

        ajouter_sondage();

    }

    //nombre de cases par défaut
    if(!Utils::issetAndNoEmpty('nbrecaseshoraires', $_SESSION)) {
        $_SESSION["nbrecaseshoraires"]=3;
    } elseif ((Utils::issetAndNoEmpty('ajoutcases')) && $_SESSION["nbrecaseshoraires"] < 9) {
        $_SESSION["nbrecaseshoraires"]=$_SESSION["nbrecaseshoraires"]+3;
    }

    //valeurs de la date du jour actuel
    $jourAJ=date("j");
    $moisAJ=date("n");
    $anneeAJ=date("Y");

    // Initialisation des jour, mois et année
    if (Utils::issetAndNoEmpty('jour', $_SESSION) === false) {
        $_SESSION['jour']= date('j');
    }
    if (Utils::issetAndNoEmpty('mois', $_SESSION) === false) {
        $_SESSION['mois']= date('n');
    }
    if (Utils::issetAndNoEmpty('annee', $_SESSION) === false) {
        $_SESSION['annee']= date('Y');
    }

    //mise a jour des valeurs de session si bouton retour a aujourd'hui
    if ((!Utils::issetAndNoEmpty('anneeavant') && !Utils::issetAndNoEmpty('anneeapres') && !Utils::issetAndNoEmpty('moisavant') && !Utils::issetAndNoEmpty('moisapres') && !Utils::issetAndNoEmpty('choixjourajout')) && !Utils::issetAndNoEmpty('choixjourretrait') || (Utils::issetAndNoEmpty('retourmois'))){
        $_SESSION["jour"]=date("j");
        $_SESSION["mois"]=date("n");
        $_SESSION["annee"]=date("Y");
    }

    //mise a jour des valeurs de session si mois avant
    if (Utils::issetAndNoEmpty('moisavant')) {
        if ($_SESSION["mois"] == 1) {
            $_SESSION["mois"]   = 12;
            $_SESSION["annee"]  = $_SESSION["annee"]-1;
        } else {
            $_SESSION["mois"] -= 1;
        }

        //On sauvegarde les heures deja entrées
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                //affichage des 5 cases horaires
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                }
            }
        }
    }

    //mise a jour des valeurs de session si mois apres
    if (Utils::issetAndNoEmpty('moisapres')) {
        if ($_SESSION["mois"] == 12) {
            $_SESSION["mois"] = 1;
            $_SESSION["annee"] += 1;
        } else {
            $_SESSION["mois"] += 1;
        }

        //On sauvegarde les heures deja entrées
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                //affichage des 5 cases horaires
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                }
            }
        }
    }

    //mise a jour des valeurs de session si annee avant
    if (Utils::issetAndNoEmpty('anneeavant')) {
        $_SESSION["annee"] -= 1;

        //On sauvegarde les heures deja entrées
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                //affichage des 5 cases horaires
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                }
            }
        }
    }

    //mise a jour des valeurs de session si annee apres
    if (Utils::issetAndNoEmpty('anneeapres')) {
        $_SESSION["annee"] += 1;

        //On sauvegarde les heures deja entrées
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                //affichage des 5 cases horaires
                for ($j = 0;$j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                }
            }
        }
    }

    //valeurs du nombre de jour dans le mois et du premier jour du mois
    $nbrejourmois = date("t", mktime(0, 0, 0, $_SESSION["mois"], 1, $_SESSION["annee"]));
    $premierjourmois = date("N", mktime(0, 0, 0, $_SESSION["mois"], 1, $_SESSION["annee"])) - 1;

    //le format du sondage est DATE
    $_SESSION["formatsondage"] = "D".$_SESSION["studsplus"];

    //traduction de la valeur du mois
    if (is_integer($_SESSION["mois"]) && $_SESSION["mois"] > 0 && $_SESSION["mois"] < 13) {
        $motmois=strftime('%B', mktime(0, 0, 0, $_SESSION["mois"], 10));
    } else {
        $motmois=strftime('%B');
    }

    //debut de la page web
    Utils::print_header ( _("Poll dates (2 on 2)") );
    bandeau_titre(_("Poll dates (2 on 2)"));

    echo '
    <form name="formulaire" action="' . Utils::get_server_name() . 'choix_date.php" method="POST" class="form-horizontal" role="form">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">

            <div class="alert alert-info">
                <p>'._("Select your dates amoung the free days (green). The selected days are in blue.").'
                <br />'. _("You can unselect a day previously selected by clicking again on it.") .'</p>
            </div>

            <div class="calendrier">
                <table class="text-center" summary="'. _('Calendar') .'">
                    <tr>
                        <td colspan="7" class="center choix_date_mois">
                            <div class="col-sm-5">
                                <button type="submit" name="moisavant" value="1" title="' . _('Previous month') . '" class="btn btn-link pull-left"><span class="glyphicon glyphicon-chevron-left"></span></button>
                                ' . $motmois . '
                                <button type="submit" name="moisapres" value="1" title="' . _('Next month') . '" class="btn btn-link pull-right"><span class="glyphicon glyphicon-chevron-right"></span></button>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" name="retourmois" value="1" title="' . _('Today') . '" class="btn btn-link"><span class="glyphicon glyphicon-refresh"></span></button>
                            </div>
                              <div class="col-sm-5">
                                <button type="submit" name="anneeavant" value="1" title="' . _('Previous year') . '" class="btn btn-link pull-left"><span class="glyphicon glyphicon-chevron-left"></span></button>
                                ' . $_SESSION["annee"] . '
                                <button type="submit" name="anneeapres" value="1" title="' . _('Next year') . '" class="btn btn-link pull-right"><span class="glyphicon glyphicon-chevron-right"></span></button>
                            </div>
                        </td>
                    </tr>
                    <tr>'."\n";

    //affichage des jours de la semaine en haut du tableau
    for($i = 0; $i < 7; $i++) {
      echo '
                        <td class="joursemaine" scope="col">'. strftime('%A',mktime(0,0,0,0, $i,10)) .'</td>';
    }

    echo '
                    </tr>'."\n";

    //ajout d'une entrée dans la variable de session qui contient toutes les dates
    if (Utils::issetAndNoEmpty('choixjourajout')) {
        if (!isset($_SESSION["totalchoixjour"])) {
          $_SESSION["totalchoixjour"]=array();
        }

        // Test pour éviter les doublons dans la variable qui contient toutes les dates
        $journeuf = true;
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true && Utils::issetAndNoEmpty('choixjourajout') === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                if ($_SESSION["totalchoixjour"][$i] == mktime(0, 0, 0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"])) {
                    $journeuf=false;
                }
            }
        }

        // Si le test est passé, alors on insere la valeur dans la variable de session qui contient les dates
        if ($journeuf && Utils::issetAndNoEmpty('choixjourajout') === true) {
            array_push ($_SESSION["totalchoixjour"],mktime (0,0,0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"]));
            sort ($_SESSION["totalchoixjour"]);
            $cle=array_search (mktime (0,0,0, $_SESSION["mois"], $_POST["choixjourajout"][0], $_SESSION["annee"]), $_SESSION["totalchoixjour"]);

            //On sauvegarde les heures deja entrées
            for ($i = 0; $i < $cle; $i++) {
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    if (Utils::issetAndNoEmpty('horaires'.$i) === true && Utils::issetAndNoEmpty($i, $_POST['horaires'.$i]) === true) {
                        $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                    }
                }
            }

            for ($i = $cle; $i < count($_SESSION["totalchoixjour"]); $i++) {
                $k = $i + 1;
                if (Utils::issetAndNoEmpty('horaires'.$i) === true && Utils::issetAndNoEmpty($i, $_POST['horaires'.$i]) === true) {
                    for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                        $_SESSION["horaires$k"][$j] = $_POST["horaires$i"][$j];
                    }
                }
            }

            unset($_SESSION["horaires$cle"]);
        }
    }

    //retrait d'une entrée dans la variable de session qui contient toutes les dates
    if (Utils::issetAndNoEmpty('choixjourretrait')) {
        //On sauvegarde les heures deja entrées
        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            //affichage des 5 cases horaires
            for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
            }
        }

        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            if ($_SESSION["totalchoixjour"][$i] == mktime(0, 0, 0, $_SESSION["mois"], $_POST["choixjourretrait"][0], $_SESSION["annee"])) {
                for ($j = $i; $j < count($_SESSION["totalchoixjour"]); $j++) {
                    $k = $j+1;
                    $_SESSION["horaires$j"] = $_SESSION["horaires$k"];
                }

                array_splice($_SESSION["totalchoixjour"], $i,1);
            }
        }
    }

    //report des horaires dans toutes les cases
    if (Utils::issetAndNoEmpty('reporterhoraires')) {
        $_SESSION["horaires0"] = $_POST["horaires0"];
        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            $j = $i+1;
            $_SESSION["horaires$j"] = $_SESSION["horaires$i"];
        }
    }

    //report des horaires dans toutes les cases
    if (Utils::issetAndNoEmpty('resethoraires')) {
        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            unset ($_SESSION["horaires$i"]);
        }
    }

    // affichage du calendrier
    echo '<tr>'."\n";

    for ($i = 0; $i < $nbrejourmois + $premierjourmois; $i++) {
        $numerojour = $i-$premierjourmois+1;

        // On saute a la ligne tous les 7 jours
        if (($i%7) == 0 && $i != 0) {
            echo '</tr><tr>'."\n";
        }

        // On affiche les jours precedants en gris et incliquables
        if ($i < $premierjourmois) {
            echo '<td class="avant"></td>'."\n";
        } else {
            if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true) {
                for ($j = 0; $j < count($_SESSION["totalchoixjour"]); $j++) {
                    //affichage des boutons BLEUS
                    if (date("j", $_SESSION["totalchoixjour"][$j]) == $numerojour && date("n", $_SESSION["totalchoixjour"][$j]) == $_SESSION["mois"] && date("Y", $_SESSION["totalchoixjour"][$j]) == $_SESSION["annee"]) {
                        echo '<td align="center" class="choisi"><input type="submit" class="bouton OFF" name="choixjourretrait[]" value="'.$numerojour.'"></td>'."\n";
                        $dejafait = $numerojour;
                    }
                }
            }

            //Si pas de bouton BLEU alors on affiche un bouton VERT ou GRIS avec le numéro du jour dessus
            if (isset($dejafait) === false || $dejafait != $numerojour){
                //bouton vert
                if (($numerojour >= $jourAJ && $_SESSION["mois"] == $moisAJ && $_SESSION["annee"] == $anneeAJ) || ($_SESSION["mois"] > $moisAJ && $_SESSION["annee"] == $anneeAJ) || $_SESSION["annee"] > $anneeAJ) {
                    echo '<td align="center" class="libre"><input type="submit" class="bouton ON" name="choixjourajout[]" value="'.$numerojour.'"></td>'."\n";
                } else { //bouton gris
                    echo '<td class="avant">'.$numerojour.'</td>'."\n";
                }
            }
        }
    }

    //fin du tableau
    echo '
                   </tr></table>
              </div>
         </div>
    </div>'."\n";

    //traitement de l'entrée des heures dans les cases texte
    $errheure = $erreur = false;
    if (Utils::issetAndNoEmpty('choixheures')) {
        //On sauvegarde les heures deja entrées
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true && Utils::issetAndNoEmpty('nbrecaseshoraires', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            //affichage des 5 cases horaires
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                }
            }
        }

        //affichage des horaires
        if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) === true && Utils::issetAndNoEmpty('nbrecaseshoraires', $_SESSION) === true) {
            for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
                //affichage des 5 cases horaires
                for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                    $case = $j + 1;

                    if (isset($_POST['horaires'.$i]) === false || isset($_POST['horaires'.$i][$j]) === false) {
                        $errheure[$i][$j]=true;
                        $erreur=true;
                        $_SESSION["horaires$i"][$j]=$_POST["horaires$i"][$j];
                        continue;
                    }

                    if ($_POST["horaires$i"][$j]=="") {
						//Si la case est vide
                        unset($_SESSION["horaires$i"][$j]);
                    } else {
                        $_SESSION["horaires$i"][$j] = $_POST["horaires$i"][$j];
                    }
                }
            }
        }
    }

    //Alignement grille selon nb d'horaires
    $days_col_md = 'col-md-6 col-md-offset-3';
    $time_col_md = 'col-md-4';
    switch ($_SESSION["nbrecaseshoraires"]) {
        case '6': $time_col_md = 'col-md-2'; $days_col_md = 'col-md-8 col-md-offset-2'; break;
        case '9': $time_col_md = 'col-sm-2'; $days_col_md = '';break;
    }

    echo '
    <div class="row" id="selected-days">
       <div class="'.$days_col_md.'">'."\n";

    //affichage de tous les jours choisis
    if (Utils::issetAndNoEmpty('totalchoixjour', $_SESSION) && (!Utils::issetAndNoEmpty('choixheures') || $erreur)) {

        echo '
            <h2>'. _("Selected days") .' :</h2>
            <div class="alert alert-info">
                <p>'._("For each selected day, you can choose, or not, meeting hours (e.g.: \"8h\", \"8:30\", \"8h-10h\", \"evening\", etc.)") .'</p>
            </div>'."\n";

        //si un seul jour et aucunes horaires choisies, : message d'erreur
        if ((Utils::issetAndNoEmpty('choixheures')) && (count($_SESSION["totalchoixjour"])=="1" && $_POST["horaires0"][0]=="" && $_POST["horaires0"][1]=="" && $_POST["horaires0"][2]=="" && $_POST["horaires0"][3]=="" && $_POST["horaires0"][4]=="")) {
            echo '<div class="alert alert-danger"><p>'. _("Enter more choices for the voters") .'</p></div>'."\n";
            $erreur=true;
        }

        $buttons_time .= '<span class="pull-right">';
        $buttons_time .= ($_SESSION["nbrecaseshoraires"] < 9) ? '<button type="submit" title="'. _("Add more hours") .'" name="ajoutcases" value="1" class="btn btn-link pull-right"><span class="glyphicon glyphicon-plus text-success"></span></button>': '';
        $buttons_time .= '
            <button type="submit" name="reporterhoraires" value="1" class="btn btn-link" title="'. _("Copy hours of the first day") .'"><span class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span></button>
            <button type="submit" name="resethoraires" value="1" class="btn btn-link" title="'. _("Remove all hours") .'"><span class="glyphicon glyphicon-remove text-danger"></span></button>
        </span>';

        //affichage de la liste des jours choisis
        for ($i=0;$i<count($_SESSION["totalchoixjour"]);$i++) {
            if($i==0) {
                echo '
            <fieldset>
                <div class="form-group">
                    '.$buttons_time;
            } else {
               echo '
            <fieldset>
                <div class="form-group">';
            }
            echo '
                    <legend>'.strftime(_("%A, den %e. %B %Y"), $_SESSION["totalchoixjour"][$i]).'</legend>'."\n";

            //affichage des cases d'horaires
            for ($j=0;$j<$_SESSION["nbrecaseshoraires"];$j++) {

                if (Utils::issetAndNoEmpty('horaires'.$i, $_SESSION) === false || Utils::issetAndNoEmpty($j, $_SESSION['horaires'.$i]) === false) {
                    if (Utils::issetAndNoEmpty('horaires'.$i, $_SESSION) === true) {
                        $_SESSION["horaires$i"][$j] = '';
                    } else {
                        $_SESSION["horaires$i"] = array();
                        $_SESSION["horaires$i"][$j] = '';
                    }
                }

                echo '
                    <div class="'.$time_col_md.'">
                        <label for="j'.$i.'-h'.$j.'" class="sr-only control-label">'. _("Time") .' '. ($j+1) .'</label>
                        <input type="text" class="form-control" title="'.strftime(_("%A, den %e. %B %Y"), $_SESSION["totalchoixjour"][$i]).' - '. _("Time") .' '. ($j+1) .'" placeholder="'. _("Time") .' '. ($j+1) .'" maxlength="11" id="j'.$i.'-h'.$j.'" name="horaires'.$i.'[]" value="'.$_SESSION["horaires$i"][$j].'" />
                    </div>'."\n";
            }

	        echo '
                </div>
            </fieldset>'."\n";
        }
        echo '
                <p class="text-right">
                    <button type="submit" name="reset" value="1" class="btn btn-default"><span class="glyphicon glyphicon-remove text-danger"></span> '. _("Remove all days") .'</button>
                    <button name="choixheures" value="'. _("Next") .'" type="submit" class="btn btn-success">'. _('Next') . '</button>
                </p>

        </div>
    </div>'."\n";
    }

    //s'il n'y a pas d'erreur et que le bouton de creation est activé, on demande confirmation
    if (!$erreur  && (Utils::issetAndNoEmpty('choixheures'))) {
        $taille_tableau=sizeof($_SESSION["totalchoixjour"])-1;
        $jour_arret = $_SESSION["totalchoixjour"][$taille_tableau]+2592000;
        $date_fin=strftime(_("%A, den %e. %B %Y"), $jour_arret);

        echo '<br />
        <div class="alert alert-info">
            <p>'. _("Your poll will expire automatically 2 days after the last date of your poll.") .'</p>
            <p>'. _("Removal date") .' : <b> '.$date_fin.'</b></p>
        </div>
        <div class="alert alert-warning">
            <p>'. _("Once you have confirmed the creation of your poll, you will be automatically redirected on the page of your poll."). '</p>
            <p>' . _("Then, you will receive quickly an email contening the link to your poll for sending it to the voters.") .'</p>
        </div>
        <p class="text-right">
            <button name="retourhoraires" value="retourhoraires" type="submit" class="btn btn-default">'. _('Back to hours') . '</button>
            <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">'. _('Create the poll') . '</button>
        </p>'."\n";
    }

    echo '<a name="bas"></a>'."\n";
    //fin du formulaire et bandeau de pied
    echo '</form>'."\n";

    bandeau_pied();

    //bouton de nettoyage de tous les jours choisis
    if (Utils::issetAndNoEmpty('reset')) {
        for ($i = 0; $i < count($_SESSION["totalchoixjour"]); $i++) {
            for ($j = 0; $j < $_SESSION["nbrecaseshoraires"]; $j++) {
                unset($_SESSION["horaires$i"][$j]);
            }
        }

        unset($_SESSION["totalchoixjour"]);
        unset($_SESSION["nbrecaseshoraires"]);
        echo '<meta http-equiv="refresh" content="0">';
    }
}
