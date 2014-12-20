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

// Send email (only once during the session) to alert admin of the change he made. ==> two modifications (comment, title, description, ...) on differents polls in the same session will generate only one mail.
$email_admin = $poll->admin_mail;
$poll_title = $poll->title;
$smtp_allowed = $config['use_smtp'];
function send_mail_admin() {
    global $email_admin;
    global $poll_title;
    global $admin_poll_id;
    global $smtp_allowed;
        if($smtp_allowed==true){
            if(!isset($_SESSION['mail_admin_sent'])) {
                Utils::sendEmail( $email_admin,
                    _("[ADMINISTRATOR] New settings for your poll") . ' ' . stripslashes( $poll_title ),
                    _("You have changed the settings of your poll. \nYou can modify this poll with this link") .
                      " :\n\n" . Utils::getUrlSondage($admin_poll_id, true) . "\n\n" .
                    _("Thanks for your confidence.") . "\n" . NOMAPPLICATION
                    );
                $_SESSION["mail_admin_sent"]=true;
            }
        }

}


// reload
// TODO OPZ Pourquoi recharger
// $dsujet= $sujets->FetchObject(false);
// $dsondage= $sondage->FetchObject(false);

if (isset($_POST['ajoutsujet'])) {
    Utils::print_header( _('Add a column') .' - ' . stripslashes($poll->title));

    bandeau_titre(_('Make your polls'));

    //on recupere les données et les sujets du sondage

    echo '
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <form name="formulaire" class="form-horizontal" action="' . Utils::getUrlSondage($admin_poll_id, true) . '" method="POST">
                <h2>' . _("Column's adding") . '</h2>'."\n";

    if ($poll->format == "A"){
        echo '
                <div class="form-group">
                    <label for="nouvellecolonne" class="col-md-6">' . _("Add a column") .' :</label>
                    <div class="col-md-6">
                        <input type="text" id="nouvellecolonne" name="nouvellecolonne" class="form-control" />
                    </div>
                </div>'."\n";
    } else {
        // ajout d'une date avec creneau horaire
        echo '
                <p>'. _("You can add a new scheduling date to your poll.").'<br />'._("If you just want to add a new hour to an existant date, put the same date and choose a new hour.") .'</p>

                <div class="form-group">
                    <label for="newdate" class="col-md-4">'. _("Day") .'</label>
                    <div class="col-md-8">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input type="text" id="newdate" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat" name="newdate" class="form-control" placeholder="'. _("dd/mm/yyyy") .'" />
                        </div>
                        <span id="dateformat" class="sr-only">'. _("(dd/mm/yyyy)") .'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="newhour" class="col-md-4">'. _("Time") .'</label>
                    <div class="col-md-8">
                        <input type="text" id="newhour" name="newhour" class="form-control" />
                    </div>
                </div>';
    }
        echo '
                <p class="text-center">
                    <button class="btn btn-default" type="submit" value="retoursondage" name="retoursondage">'. _('Back to the poll'). '</button>
                    <button type="submit" name="ajoutercolonne" class="btn btn-success">'. _('Add a column'). '</button>
                </p>
            </form>
            </div>
        </div>';

    bandeau_pied();

    die();
}

//action si bouton confirmation de suppression est activé
if (isset($_POST["confirmesuppression"])) {
    $nbuser=$user_studs->RecordCount();
    $date=date('H:i:s d/m/Y:');

    if (Utils::remove_sondage($connect, $poll_id)) {
        // on ecrit dans le fichier de logs la suppression du sondage
        error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'admin/logs_studs.txt');

        // Email sent
        send_mail_admin();
        //affichage de l'ecran de confirmation de suppression de sondage
        Utils::print_header(_("Your poll has been removed!"));

        bandeau_titre(_("Make your polls"));

        echo '
        <div class="alert alert-success text-center">
            <h2>' . _("Your poll has been removed!") . '</h2>
            <p>' . _('Back to the homepage of ') . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
        </div>
    </form>'."\n";

        bandeau_pied();

        die();
    }
}

$nbcolonnes = count($sujets);
$nblignes = count($users);

//si il n'y a pas suppression alors on peut afficher normalement le tableau



//action quand on ajoute une colonne au format AUTRE
if (isset($_POST["ajoutercolonne"]) && !empty($_POST['nouvellecolonne']) && $poll->format == "A") {
    $nouveauxsujets=$dsujet->sujet;

    //on rajoute la valeur a la fin de tous les sujets deja entrés
    $nouveauxsujets.=",";
    $nouveauxsujets.=str_replace(","," ",$_POST["nouvellecolonne"]);
    $nouveauxsujets = htmlentities(html_entity_decode($nouveauxsujets, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

    //mise a jour avec les nouveaux sujets dans la base
    $sql = 'UPDATE sujet_studs SET sujet = '.$connect->Param('nouveauxsujets').' WHERE id_sondage = '.$connect->Param('numsondage');
    $sql = $connect->Prepare($sql);
    if ($connect->Execute($sql, array($nouveauxsujets, $poll_id))) {
        send_mail_admin();
    }
}


// [begin] action quand on ajoute une colonne au format DATE
if (isset($_POST['ajoutercolonne']) && $dsondage->format == 'D') {

    if (!empty($_POST["newdate"])) {
        $new_choice = mktime(0, 0, 0, substr($_POST["newdate"],3,2), substr($_POST["newdate"],0,2), substr($_POST["newdate"],6,4));

        if (!empty($_POST["newhour"])){
            $new_choice .= '@' . $_POST["newhour"];
        }





        // TODO OPZ Delete the code below
        // TODO OPZ Insert new choice
        // TODO OPZ Update users votes (add "0" in the right column^^)



        //on rajoute la valeur dans les valeurs
        $datesbase = explode(",",$dsujet->sujet);
        $taillebase = sizeof($datesbase);

        //recherche de l'endroit de l'insertion de la nouvelle date dans les dates deja entrées dans le tableau
        if ($nouvelledate < $datesbase[0]) {
            $cleinsertion = 0;
        } elseif ($nouvelledate > $datesbase[$taillebase-1]) {
            $cleinsertion = count($datesbase);
        } else {
            for ($i = 0; $i < count($datesbase); $i++) {
                $j = $i + 1;
                if ($nouvelledate > $datesbase[$i] && $nouvelledate < $datesbase[$j]) {
                    $cleinsertion = $j;
                }
            }
        }

        array_splice($datesbase, $cleinsertion, 0, $nouvelledate);
        $cle = array_search($nouvelledate, $datesbase);
        $dateinsertion = '';
        for ($i = 0; $i < count($datesbase); $i++) {
            $dateinsertion.=",";
            $dateinsertion.=$datesbase[$i];
        }

        $dateinsertion = substr("$dateinsertion", 1);

        //mise a jour avec les nouveaux sujets dans la base
        //if (isset($erreur_ajout_date) && !$erreur_ajout_date){
            $sql = 'UPDATE sujet_studs SET sujet = '.$connect->Param('dateinsertion').' WHERE id_sondage = '.$connect->Param('numsondage');
            $sql = $connect->Prepare($sql);
            $connect->Execute($sql, array($dateinsertion, $poll_id));

            if ($nouvelledate > strtotime($dsondage->date_fin)) {
                $date_fin=$nouvelledate+200000;
                $sql = 'UPDATE sondage SET date_fin = '.$connect->Param('date_fin').' WHERE id_sondage = '.$connect->Param('numsondage');
                $sql = $connect->Prepare($sql);
                $connect->Execute($sql, array($date_fin, $poll_id));
            }
        //}

        //mise a jour des reponses actuelles correspondant au sujet ajouté
        $sql = 'UPDATE user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users='.$connect->Param('id_users');
        $sql = $connect->Prepare($sql);
        while ($data = $user_studs->FetchNextObject(false)) {
            $ensemblereponses=$data->reponses;
            $newcar = '';

            //parcours de toutes les réponses actuelles
            for ($j = 0; $j < $nbcolonnes; $j++) {
                $car=substr($ensemblereponses,$j,1);

                //si les reponses ne concerne pas la colonne ajoutée, on concatene
                if ($j==$cle) {
                    $newcar.="0";
                }

                $newcar.=$car;
            }

            //mise a jour des reponses utilisateurs dans la base
            if (isset($erreur_ajout_date) && !$erreur_ajout_date){
                $connect->Execute($sql, array($newcar, $data->nom, $data->id_users));
            }
        }

        //Email sent to the admin
        send_mail_admin();

    } else {
        $erreur_ajout_date="yes";
    }
}
// [end] action quand on ajoute une colonne au format DATE


//on teste pour voir si une ligne doit etre modifiée
$testmodifier = false;
$testligneamodifier = false;


//suppression de colonnes dans la base
for ($i = 0; $i < $nbcolonnes; $i++) {
    if ((isset($_POST["effacecolonne$i"])) && $nbcolonnes > 1){
        $sujets = explode(",",$dsujet->sujet);
        //sort($toutsujet, SORT_NUMERIC);
        $j = 0;
        $nouveauxsujets = '';

        //parcours de tous les sujets actuels
        while (isset($sujets[$j])) {
            //si le sujet n'est pas celui qui a été effacé alors on concatene
            if ($i != $j) {
                $nouveauxsujets .= ',';
                $nouveauxsujets .= $sujets[$j];
            }

            $j++;
        }

        //on enleve la virgule au début
        $nouveauxsujets = substr("$nouveauxsujets", 1);

        //nettoyage des reponses actuelles correspondant au sujet effacé
        $compteur = 0;
        $sql = 'UPDATE user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
        $sql = $connect->Prepare($sql);

        while ($data = $user_studs->FetchNextObject(false)) {
            $newcar = '';
            $ensemblereponses = $data->reponses;

            //parcours de toutes les réponses actuelles
            for ($j = 0; $j < $nbcolonnes; $j++) {
                $car=substr($ensemblereponses, $j, 1);
                //si les reponses ne concerne pas la colonne effacée, on concatene
                if ($i != $j) {
                    $newcar .= $car;
                }
            }

            $compteur++;

            //mise a jour des reponses utilisateurs dans la base
            $connect->Execute($sql, array($newcar, $data->nom, $data->id_users));
        }

        //mise a jour des sujets dans la base
        $sql = 'UPDATE sujet_studs SET sujet = '.$connect->Param('nouveauxsujets').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);
        $connect->Execute($sql, array($nouveauxsujets, $poll_id));
    }
}



// Table headers
$thead = '<thead>';

// Button in the first td to avoid remove col on "Return" keypress)
$tr_add_remove_col = '<tr><td role="presentation"><button type="submit" class="invisible" name="boutonp" ></button></td>';

$border = array(); // bordure pour distinguer les mois
$td_headers = array(); // for a11y, headers="M1 D4 H5" on each td
$radio_title = array(); // date for

// Display dates poll
if ($poll->format == "D") {

    $tr_months = '<tr><th role="presentation"></th>';
    $tr_days = '<tr><th role="presentation"></th>';
    $tr_hours = '<tr><th role="presentation"></th>';

    // Headers
    $colspan_month = 1;
    $colspan_day = 1;

    foreach ($sujets as $i=>$sujet) {

        // Current date
        $horoCur = explode('@', $sujet->sujet); //horoCur[0] = date, horoCur[1] = hour
        if (isset($sujets[$i+1])){
            $next = $sujets[$i+1]->sujet;
            $horoNext = explode('@', $next);
        }
        $border[$i] = false;
        $radio_title[$i] = strftime($date_format['txt_short'], $horoCur[0]);

        // Months
        $td_headers[$i] = 'M'.($i+1-$colspan_month);

        if (isset($sujets[$i+1]) && strftime("%B", $horoCur[0]) == strftime("%B", $horoNext[0]) && strftime("%Y", $horoCur[0]) == strftime("%Y", $horoNext[0])){
            $colspan_month++;
        } else {
            $border[$i] = true;
            $tr_months .= '<th colspan="'.$colspan_month.'" class="bg-primary month" id="M'.($i+1-$colspan_month).'">'.strftime("%B",$horoCur[0]).' '.strftime("%Y", $horoCur[0]).'</th>';
            $colspan_month=1;
        }

        // Days
        $td_headers[$i] .= ' D'.($i+1-$colspan_day);

        if (isset($sujets[$i+1]) && strftime($date_format['txt_day'],$horoCur[0])==strftime($date_format['txt_day'],$horoNext[0]) && strftime("%B",$horoCur[0])==strftime("%B",$horoNext[0])){
            $colspan_day++;
        } else {
            $rbd = ($border[$i]) ? ' rbd' : '';
            $tr_days .= '<th colspan="'.$colspan_day.'" class="bg-primary day'.$rbd.'" id="D'.($i+1-$colspan_day).'">'.strftime($date_format['txt_day'],$horoCur[0]).'</th>';
            $colspan_day=1;
        }

        // Hours
        $rbd = ($border[$i]) ? ' rbd' : '';
        if ($horoCur[1] !== "") {
                $tr_hours .= '<th class="bg-info'.$rbd.'" id="H'.$i.'" title="'.$horoCur[1].'">'.$horoCur[1].'</th>';
                $radio_title[$i] .= ' - '.$horoCur[1];
                $td_headers[$i] .= ' H'.$i;
        } else {
                $tr_hours .= '<th class="bg-info'.$rbd.'"></th>';
        }

        // Remove col
        $tr_add_remove_col .= (count($sujets) > 2 ) ? '<td headers="'.$td_headers[$i].'"><button type="submit" name="effacecolonne'.$i.'" class="btn btn-link btn-sm" title="' . _('Remove the column') . ' ' .$radio_title[$i]. '"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">'. _("Remove") .'</span></button></td>' : '<td role="presentation"></td>';

    }

    $border[count($border)-1] = false; // suppression de la bordure droite du dernier mois

    $tr_months .= '<th></th></tr>';
    $tr_days .= '<th></th></tr>';
    $tr_hours .= '<th></th></tr>';

    // Add col
    $tr_add_remove_col .= '<td><button type="submit" name="ajoutsujet" class="btn btn-link btn-sm" title="'. _('Add a column') . '"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add a column") .'</span></button></td></tr>';

    $thead = "\n".$tr_add_remove_col."\n".$tr_months."\n".$tr_days."\n".$tr_hours."\n";

// Subjects poll
} else {
    $tr_subjects = '<tr><th role="presentation"></th>';

    foreach ($sujets as $i=>$sujet) {

        $td_headers[$i]='';$radio_title[$i]=''; // init before concatenate

        // Subjects
        preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/',$sujet->sujet,$md_a_img);  // Markdown [![alt](src)](href)
        preg_match_all('/!\[(.*?)\]\((.*?)\)/',$sujet->sujet,$md_img);                 // Markdown ![alt](src)
        preg_match_all('/\[(.*?)\]\((.*?)\)/',$sujet->sujet,$md_a);                    // Markdown [text](href)
        if (isset($md_a_img[2][0]) && $md_a_img[2][0]!='' && isset($md_a_img[3][0]) && $md_a_img[3][0]!='') { // [![alt](src)](href)

            $th_subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0]!='') ? stripslashes($md_a_img[1][0]) : _("Choice") .' '.($i+1);
            $th_subject_html = '<a href="'.$md_a_img[3][0].'"><img src="'.$md_a_img[2][0].'" class="img-responsive" alt="'.$th_subject_text.'" /></a>';

        } elseif (isset($md_img[2][0]) && $md_img[2][0]!='') { // ![alt](src)

            $th_subject_text = (isset($md_img[1][0]) && $md_img[1][0]!='') ? stripslashes($md_img[1][0]) : _("Choice") .' '.($i+1);
            $th_subject_html = '<img src="'.$md_img[2][0].'" class="img-responsive" alt="'.$th_subject_text.'" />';

        } elseif (isset($md_a[2][0]) && $md_a[2][0]!='') { // [text](href)

            $th_subject_text = (isset($md_a[1][0]) && $md_a[1][0]!='') ? stripslashes($md_a[1][0]) : _("Choice") .' '.($i+1);
            $th_subject_html = '<a href="'.$md_a[2][0].'">'.$th_subject_text.'</a>';

        } else { // text only

            $th_subject_text = stripslashes($sujet->sujet);
            $th_subject_html = $th_subject_text;

        }
        $tr_subjects .= '<th class="bg-info" id="S'.$i.'" title="'.$th_subject_text.'">'.$th_subject_html.'</th>';

        $border[$i] = false;
        $td_headers[$i] .= 'S'.$i;
        $radio_title[$i] .= $th_subject_text;

        // Remove col
        $tr_add_remove_col .= '<td headers="'.$td_headers[$i].'"><button type="submit" name="effacecolonne'.$i.'" class="btn btn-link btn-sm" title="' . _('Remove the column') . ' '. $radio_title[$i] .'"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">' . _('Remove') .'</span></button></td>';
    }

    // Add col
    $tr_add_remove_col .= '<td><button type="submit" name="ajoutsujet" class="btn btn-link btn-sm" title="'. _('Add a column') . '"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _("Add a column") .'</span></button></td></tr>';

    $thead = $tr_add_remove_col.$tr_subjects.'<th></th></tr>';
}

// Print headers
