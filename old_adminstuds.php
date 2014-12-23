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


//on teste pour voir si une ligne doit etre modifiée
$testmodifier = false;
$testligneamodifier = false;



// Button in the first td to avoid remove col on "Return" keypress)
$tr_add_remove_col = '<tr><td role="presentation"><button type="submit" class="invisible" name="boutonp" ></button></td>';

$border = array(); // bordure pour distinguer les mois
$td_headers = array(); // for a11y, headers="M1 D4 H5" on each td
$radio_title = array(); // date for

if ($poll->format == "A") {
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
