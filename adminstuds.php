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

//setlocale(LC_TIME, "fr_FR");
include_once __DIR__ . '/app/inc/functions.php';

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Initialisation des variables
$numsondageadmin = false;
$sondage = false;

// recuperation du numero de sondage admin (24 car.) dans l'URL
if (issetAndNoEmpty('sondage', $_GET) && is_string($_GET['sondage']) && strlen($_GET['sondage']) === 24) {
    $numsondageadmin=$_GET["sondage"];
    //on découpe le résultat pour avoir le numéro de sondage (16 car.)
    $numsondage=substr($numsondageadmin, 0, 16);
}

if (preg_match(";[\w\d]{24};i", $numsondageadmin)) {
    $sql = 'SELECT * FROM sondage WHERE id_sondage_admin = '.$connect->Param('numsondageadmin');
    $sql = $connect->Prepare($sql);
    $sondage = $connect->Execute($sql, array($numsondageadmin));

    if ($sondage !== false) {
        $sql = 'SELECT * FROM sujet_studs WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);
        $sujets = $connect->Execute($sql, array($numsondage));

        $sql = 'SELECT * FROM user_studs WHERE id_sondage = '.$connect->Param('numsondage').' order by id_users';
        $sql = $connect->Prepare($sql);
        $user_studs = $connect->Execute($sql, array($numsondage));
    }
}

//verification de l'existence du sondage, s'il n'existe pas on met une page d'erreur
if (!$sondage || $sondage->RecordCount() != 1){
    print_header( _("Error!"));
  
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alert-warning">
        <h2>' . _("This poll doesn't exist !") . '</h2>
        <p>' . _('Back to the homepage of ') . ' <a href="' . get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    bandeau_pied();

    die();
}

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);


//si la valeur du nouveau titre est valide et que le bouton est activé
$adresseadmin = $dsondage->mail_admin;

if (isset($_POST["boutonnouveautitre"])) {
    if (issetAndNoEmpty('nouveautitre') === false) {
        $err |= TITLE_EMPTY;
    } else {
        //modification de la base SQL avec le nouveau titre
        $nouveautitre = htmlentities(html_entity_decode($_POST['nouveautitre'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        $sql = 'UPDATE sondage SET titre = '.$connect->Param('nouveautitre').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        //envoi du mail pour prevenir l'admin de sondage
        if ($connect->Execute($sql, array($nouveautitre, $numsondage))) {
            sendEmail( $adresseadmin,
                _("[ADMINISTRATOR] New title for your poll") . ' ' . NOMAPPLICATION,
                _("You have changed the title of your poll. \nYou can modify this poll with this link") .
                " :\n\n".getUrlSondage($numsondageadmin, true)."\n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

// si le bouton est activé, quelque soit la valeur du champ textarea
if (isset($_POST["boutonnouveauxcommentaires"])) {
    if (issetAndNoEmpty('nouveautitre') === false) {
        $err |= COMMENT_EMPTY;
    } else {
        $commentaires = htmlentities(html_entity_decode($_POST['nouveauxcommentaires'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        //modification de la base SQL avec les nouveaux commentaires
        $sql = 'UPDATE sondage SET commentaires = '.$connect->Param('commentaires').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        if ($connect->Execute($sql, array($commentaires, $numsondage))) {
            //envoi du mail pour prevenir l'admin de sondage
            sendEmail( $adresseadmin,
                _("[ADMINISTRATOR] New comments for your poll") . ' ' . NOMAPPLICATION,
                _("You have changed the comments of your poll. \nYou can modify this poll with this link") .
                " :\n\n".getUrlSondage($numsondageadmin, true)." \n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

//si la valeur de la nouvelle adresse est valide et que le bouton est activé
if (isset($_POST["boutonnouvelleadresse"])) {
    if (issetAndNoEmpty('nouvelleadresse') === false || validateEmail($_POST["nouvelleadresse"]) === false) {
       $err |= INVALID_EMAIL;
    } else {
        $nouvelleadresse = htmlentities(html_entity_decode($_POST['nouvelleadresse'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        //modification de la base SQL avec la nouvelle adresse
        $sql = 'UPDATE sondage SET mail_admin = '.$connect->Param('nouvelleadresse').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        if ($connect->Execute($sql, array($nouvelleadresse, $numsondage))) {
            //envoi du mail pour prevenir l'admin de sondage
            sendEmail( $_POST['nouvelleadresse'],
                _("[ADMINISTRATOR] New email address for your poll") . ' ' . NOMAPPLICATION,
                _("You have changed your email address in your poll. \nYou can modify this poll with this link") .
                " :\n\n".getUrlSondage($numsondageadmin, true)."\n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

// reload
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

if (isset($_POST["ajoutsujet"])) {
    print_header('');

    bandeau_titre(_("Make your polls"));

    //on recupere les données et les sujets du sondage

    echo '
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <form name="formulaire" class="form-horizontal" action="'.getUrlSondage($numsondageadmin, true).'" method="POST">
                <h2>' . _("Column's adding") . '</h2>'."\n";

    if ($dsondage->format=="A"||$dsondage->format=="A+"){
        echo '
                <div class="form-group">
                    <label for="nouvellecolonne" class="col-md-6">' . _("Add a new column") .' :</label>
                    <div class="col-md-6">
                        <input type="text" id="nouvellecolonne" name="nouvellecolonne" class="form-control" />
                    </div>
                </div>'."\n";
    } else {
        //ajout d'une date avec creneau horaire
        echo '
                <p>'. _("You can add a new scheduling date to your poll.")._("If you just want to add a new hour to an existant date, put the same date and choose a new hour.") .'</p>
            
                <div class="form-group">
                    <label for="newdate" class="col-md-5">'. _("Add a date") .' :</label>
                    <div class="col-md-7">
                        <div class="input-group date">
                            <input type="text" id="newdate" data-date-format="'. _("dd/mm/yyyy") .'" aria-describedby="dateformat" name="newdate" class="form-control" placeholder="'. _("dd/mm/yyyy") .'" /><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <span id="dateformat" class="sr-only">'. _("(DD/MM/YYYY)") .'</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="newhour" class="col-md-5">'. _("Hour") .' :</label>
                    <div class="col-md-7">
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

if (isset($_POST["suppressionsondage"])) {
    print_header('');

    bandeau_titre(_("Confirm removal of your poll"));
    
    echo '
        <form name="formulaire" action="'.getUrlSondage($numsondageadmin, true).'" method="POST">
        <div class="alert alert-warning text-center">
            <h2>' . _("Confirm removal of your poll") . '</h2>
            <p><button class="btn btn-default" type="submit" value="" name="annullesuppression">'._("Keep this poll!").'</button>
            <button type="submit" name="confirmesuppression" value="" class="btn btn-danger">'._("Remove this poll!").'</button></p>
        </div>
        </form>';

    bandeau_pied();

    die();
}

//action si bouton confirmation de suppression est activé
if (isset($_POST["confirmesuppression"])) {
    $nbuser=$user_studs->RecordCount();
    $date=date('H:i:s d/m/Y:');

    if ( remove_sondage( $connect, $numsondage ) ) {
        // on ecrit dans le fichier de logs la suppression du sondage
        error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'admin/logs_studs.txt');

        //envoi du mail a l'administrateur du sondage
        sendEmail( $adresseadmin,
            _("[ADMINISTRATOR] Removing of your poll") . ' ' . NOMAPPLICATION,
            _("You have removed your poll. \nYou can make new polls with this link") .
            " :\n\n".get_server_name()."index.php \n\n" .
            _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );

        //affichage de l'ecran de confirmation de suppression de sondage
        print_header('');
        
        bandeau_titre(_("Make your polls"));

        echo '
        <div class="alert alert-success text-center">
            <h2>' . _("Your poll has been removed!") . '</h2>
            <p>' . _('Back to the homepage of ') . ' <a href="' . get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
        </div>
    </form>'."\n";
    
        bandeau_pied();
        
        die();
    }
}

// quand on ajoute un commentaire utilisateur
if (isset($_POST['ajoutcomment'])) {
    if (issetAndNoEmpty('commentuser') === false) {
        $err |= COMMENT_USER_EMPTY;
    } else {
        $comment_user = htmlentities(html_entity_decode($_POST["commentuser"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }

    if(issetAndNoEmpty('comment') === false) {
        $err |= COMMENT_EMPTY;
    }

    if (issetAndNoEmpty('comment') && !is_error(COMMENT_EMPTY) && !is_error(NO_POLL) && !is_error(COMMENT_USER_EMPTY)) {
        $comment = htmlentities(html_entity_decode($_POST["comment"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        $sql = 'INSERT INTO comments (id_sondage, comment, usercomment) VALUES ('.
            $connect->Param('id_sondage').','.
            $connect->Param('comment').','.
            $connect->Param('comment_user').')';
        $sql = $connect->Prepare($sql);

        $comments = $connect->Execute($sql, array($numsondage, $comment, $comment_user));
        if ($comments === false) {
            $err |= COMMENT_INSERT_FAILED;
        }
    }
}


//s'il existe on affiche la page normale
// DEBUT DE L'AFFICHAGE DE LA PAGE HTML
print_header('');

bandeau_titre(_("Make your polls"));

//affichage du titre du sondage
$titre=str_replace("\\","",$dsondage->titre);
echo '
        <div class="jumbotron">
            <div class="row">
                <div class="col-md-7">
                    <h2>'.stripslashes($titre).'</h2>
                </div>
                <div class="col-md-5">
                    <form name="formulaire4" action="#" method="POST">
                    <div class="btn-group pull-right">
                        <button onclick="javascript:print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> ' . _('Print') . '</button>
                        <button onclick="window.location.href=\''.get_server_name().'exportcsv.php?numsondage=' . $numsondage . '\';return false;" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> ' . _('Export to CSV') . '</button>
                        <button type="submit" id="suppressionsondage" name="suppressionsondage" value="" class="btn btn-danger" title="'. _("Remove the poll") .'"><span class="glyphicon glyphicon-trash"></span></button>
                    </div>
                    </form>
                </div>           
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="control-label">'. _("Initiator of the poll") .' :</label>
                        <p class="form-control-static"> '.stripslashes($dsondage->nom_admin).'</p>
                    </div>
                    <div class="form-group">
                        <label class="control-label">'. _("Initiator's email:") .'</label>
                        <p class="form-control-static"> '.stripslashes($dsondage->mail_admin).'</p>
                    </div>
                </div>';
            
//affichage de la description du sondage
if ($dsondage->commentaires) {
    $commentaires = $dsondage->commentaires;
    $commentaires=nl2br(str_replace("\\","",$commentaires));
    echo '
                <div class="form-group col-md-7">
                    <label class="control-label">'._("Description: ") .'</label><br />
                    <p class="form-control-static well">'. $commentaires .'</p>
                </div>';
}
echo '
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="public-link">'._("Public link of the pool") .' <a href="'.getUrlSondage($dsondage->id_sondage).'" class="glyphicon glyphicon-link"></a> : </label>
                    <input class="form-control" id="public-link" type="text" readonly="readonly" value="'.getUrlSondage($dsondage->id_sondage).'" />
                </div>
                <div class="form-group col-md-5">
                    <label for="admin-link">'._("Admin link of the pool") .' <a href="'.getUrlSondage($numsondageadmin, true).'" class="glyphicon glyphicon-link"></a> : </label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="'.getUrlSondage($numsondageadmin, true).'" />
                </div>
            </div>
        </div>'."\n"; // .jumbotron

$nbcolonnes = substr_count($dsujet->sujet, ',') + 1;
$nblignes = $user_studs->RecordCount();

//si il n'y a pas suppression alors on peut afficher normalement le tableau

//action si le bouton participer est cliqué
if (isset($_POST["boutonp"])) {
    //si on a un nom dans la case texte
    if (issetAndNoEmpty('nom')){
        $nouveauchoix = '';
        $erreur_prenom = false;

        for ($i=0;$i<$nbcolonnes;$i++){
            // radio checked 1 = Yes, 2 = Ifneedbe, 0 = No
            if (isset($_POST["choix$i"])) {
                switch ($_POST["choix$i"]) {
                    case 1: $nouveauchoix .= "1";break;
                    case 2: $nouveauchoix .= "2";break;
                    default: $nouveauchoix .= "0";break;
                }
			}
        }

        $nom = htmlentities(html_entity_decode($_POST["nom"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        
        while($user = $user_studs->FetchNextObject(false)) {
            if ($nom == $user->nom){
                $erreur_prenom="yes";
            }
        }

        // Ecriture des choix de l'utilisateur dans la base
        if (!$erreur_prenom) {
            $sql = 'INSERT INTO user_studs (nom, id_sondage, reponses) VALUES ('.
                $connect->Param('nom').','.
                $connect->Param('numsondage').','.
                $connect->Param('nouveauchoix').')';

            $sql = $connect->Prepare($sql);
            $connect->Execute($sql, array($nom, $numsondage, $nouveauchoix));
        }
    }
}


//action quand on ajoute une colonne au format AUTRE
if (isset($_POST["ajoutercolonne"]) && issetAndNoEmpty('nouvellecolonne') && ($dsondage->format == "A" || $dsondage->format == "A+")) {
    $nouveauxsujets=$dsujet->sujet;

    //on rajoute la valeur a la fin de tous les sujets deja entrés
    $nouveauxsujets.=",";
    $nouveauxsujets.=str_replace(","," ",$_POST["nouvellecolonne"]);
    $nouveauxsujets = htmlentities(html_entity_decode($nouveauxsujets, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

    //mise a jour avec les nouveaux sujets dans la base
    $sql = 'UPDATE sujet_studs SET sujet = '.$connect->Param('nouveauxsujets').' WHERE id_sondage = '.$connect->Param('numsondage');
    $sql = $connect->Prepare($sql);
    if ($connect->Execute($sql, array($nouveauxsujets, $numsondage))) {
      //envoi d'un mail pour prévenir l'administrateur du changement
      sendEmail( "$adresseadmin", "" . _("[ADMINISTRATOR] New column for your poll").NOMAPPLICATION, "" .
          _("You have added a new column in your poll. \nYou can inform the voters of this change with this link") .
          " : \n\n".getUrlSondage($numsondage)." \n\n " . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
    }
}


//action quand on ajoute une colonne au format DATE
if (isset($_POST["ajoutercolonne"]) && ($dsondage->format == "D" || $dsondage->format == "D+")) {
    $nouveauxsujets=$dsujet->sujet;

    if (isset($_POST["newdate"]) && $_POST["newdate"] != "vide") {
        $nouvelledate=mktime(0, 0, 0, substr($_POST["newdate"],3,2), substr($_POST["newdate"],0,2), substr($_POST["newdate"],6,4));

        if (isset($_POST["newhour"]) && $_POST["newhour"]!="vide"){
            $nouvelledate.="@";
            $nouvelledate.=$_POST["newhour"];
        }

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
            $connect->Execute($sql, array($dateinsertion, $numsondage));

            if ($nouvelledate > strtotime($dsondage->date_fin)) {
                $date_fin=$nouvelledate+200000;
                $sql = 'UPDATE sondage SET date_fin = '.$connect->Param('date_fin').' WHERE id_sondage = '.$connect->Param('numsondage');
                $sql = $connect->Prepare($sql);
                $connect->Execute($sql, array($date_fin, $numsondage));
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

        //envoi d'un mail pour prévenir l'administrateur du changement
        $adresseadmin = $dsondage->mail_admin;

        sendEmail( $adresseadmin,
            _("[ADMINISTRATOR] New column for your poll"),
            _("You have added a new column in your poll. \nYou can inform the voters of this change with this link").
            " : \n\n".getUrlSondage($numsondage)." \n\n " . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
            
    } else {
        $erreur_ajout_date="yes";
    }
}


//suppression de ligne dans la base
for ($i = 0; $i < $nblignes; $i++) {
    if (isset($_POST["effaceligne$i"])) {
        $compteur=0;
        $sql = 'DELETE FROM user_studs WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
        $sql = $connect->Prepare($sql);

        while ($data=$user_studs->FetchNextObject(false)) {
            if ($compteur==$i){
                $connect->Execute($sql, array($data->nom, $data->id_users));
            }

            $compteur++;
        }
    }
}


//suppression d'un commentaire utilisateur
$sql = 'SELECT * FROM comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
$sql = $connect->Prepare($sql);
$comment_user = $connect->Execute($sql, array($numsondage));
$i = 0;
while ($dcomment = $comment_user->FetchNextObject(false)) {
    if (isset($_POST['suppressioncomment'.$i])) {
        $sql = 'DELETE FROM comments WHERE id_comment = '.$connect->Param('id_comment');
        $sql = $connect->Prepare($sql);
        $connect->Execute($sql, array($dcomment->id_comment));
    }

    $i++;
}


//on teste pour voir si une ligne doit etre modifiée
$testmodifier = false;
$testligneamodifier = false;

for ($i = 0; $i < $nblignes; $i++) {
    if (isset($_POST["modifierligne$i"])) {
        $ligneamodifier=$i;
        $testligneamodifier="true";
    }

    //test pour voir si une ligne est a modifier
    if (isset($_POST["validermodifier$i"])) {
        $modifier=$i;
        $testmodifier="true";
    }
}


//si le test est valide alors on affiche des checkbox pour entrer de nouvelles valeurs
if ($testmodifier) {
    $nouveauchoix = '';
    for ($i = 0; $i < $nbcolonnes; $i++) {
        // radio checked 1 = Yes, 2 = Ifneedbe, 0 = No
        if (isset($_POST["choix$i"])) {
            switch ($_POST["choix$i"]) {
                case 1: $nouveauchoix .= "1";break;
                case 2: $nouveauchoix .= "2";break;
                default: $nouveauchoix .= "0";break;
            }
        }
    }

    $compteur=0;

    while ($data=$user_studs->FetchNextObject(false)) {
        //mise a jour des données de l'utilisateur dans la base SQL
        if ($compteur==$modifier) {
            $sql = 'UPDATE user_studs SET reponses = '.$connect->Param('reponses').' WHERE nom = '.$connect->Param('nom').' AND id_users = '.$connect->Param('id_users');
            $sql = $connect->Prepare($sql);
            $connect->Execute($sql, array($nouveauchoix, $data->nom, $data->id_users));
        }

        $compteur++;
    }
}


//suppression de colonnes dans la base
for ($i = 0; $i < $nbcolonnes; $i++) {
    if ((isset($_POST["effacecolonne$i"])) && $nbcolonnes > 1){
        $toutsujet = explode(",",$dsujet->sujet);
        //sort($toutsujet, SORT_NUMERIC);
        $j = 0;
        $nouveauxsujets = '';

        //parcours de tous les sujets actuels
        while (isset($toutsujet[$j])) {
            //si le sujet n'est pas celui qui a été effacé alors on concatene
            if ($i != $j) {
                $nouveauxsujets .= ',';
                $nouveauxsujets .= $toutsujet[$j];
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
        $connect->Execute($sql, array($nouveauxsujets, $numsondage));
    }
}


//recuperation des donnes de la base
$sql = 'SELECT * FROM sondage WHERE id_sondage_admin = '.$connect->Param('numsondageadmin');
$sql = $connect->Prepare($sql);
$sondage = $connect->Execute($sql, array($numsondageadmin));

if ($sondage !== false) {
    $sql = 'SELECT * FROM sujet_studs WHERE id_sondage = '.$connect->Param('numsondage');
    $sql = $connect->Prepare($sql);
    $sujets = $connect->Execute($sql, array($numsondage));

    $sql = 'SELECT * FROM user_studs WHERE id_sondage = '.$connect->Param('numsondage').' order by id_users';
    $sql = $connect->Prepare($sql);
    $user_studs = $connect->Execute($sql, array($numsondage));
} else {
  
    print_header(_("Error!"));
  
    bandeau_titre(_("Error!"));
  
    echo '
    <div class="alert alert-warning">
        <h2>' . _("This poll doesn't exist !") . '</h2>
        <p>' . _('Back to the homepage of ') . ' <a href="' . get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    bandeau_pied();

    die();
}

//on recupere les données et les sujets du sondage
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

$toutsujet=explode(",",$dsujet->sujet);
$toutsujet=str_replace("@","<br />",$toutsujet);
$toutsujet=str_replace("°","'",$toutsujet);
$nbcolonnes=substr_count($dsujet->sujet,',')+1;

echo '
<form name="formulaire" action="'.getUrlSondage($numsondageadmin, true).'" method="POST">

<div class="alert alert-info">
    <p>' . _('As poll administrator, you can change all the lines of this poll with this button ').'<span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _('Edit') . '</span>,
       ' . _(' remove a column or a line with ') . '<span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">' . _('Remove') . '</span>
       ' . _('and add a new column with '). '<span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _('Add a column') . '</span></p>
    <p>' . _('Finally, you can change the informations of this poll like the title, the comments or your email address.') . '</p>
</div>';

//debut de l'affichage de résultats
echo '
    <div id="tableContainer" class="tableContainer">
    <table class="results">
        <thead>';

//reformatage des données des sujets du sondage
$toutsujet=explode(",",$dsujet->sujet);
echo '<tr>
<td></td>'."\n";

//boucle pour l'affichage des boutons de suppression de colonne
for ($i = 0; isset($toutsujet[$i]); $i++) {
    echo '<td headers="'.$td_headers[$i].'"><button type="submit" name="effacecolonne'.$i.'" class="btn btn-link btn-sm" title="' . _('Remove the column') . '"><span class="glyphicon glyphicon-remove text-danger"></span></button></td>'."\n";
}
    echo '<td><button type="submit" name="ajoutsujet" class="btn btn-link btn-sm" title="'. _('Add a column') . '"><span class="glyphicon glyphicon-plus text-success"></span></button></td>
</tr>'."\n";

//si le sondage est un sondage de date
if ($dsondage->format=="D"||$dsondage->format=="D+") {

    //affichage des sujets du sondage
    echo '<tr>
<th role="presentation"></th>'."\n";

	$border = array();
	$td_headers = array();
    
    //affichage des mois et années
    $colspan = 1;
    for ($i = 0; $i < count($toutsujet); $i++) {
        $current = $toutsujet[$i];

        if (strpos($toutsujet[$i], '@') !== false) {
            $current = substr($toutsujet[$i], 0, strpos($toutsujet[$i], '@'));
        }

        if (isset($toutsujet[$i+1]) && strpos($toutsujet[$i+1], '@') !== false) {
            $next = substr($toutsujet[$i+1], 0, strpos($toutsujet[$i+1], '@'));
        } elseif (isset($toutsujet[$i+1])) {
            $next = $toutsujet[$i+1];
        }

        if (isset($toutsujet[$i+1]) && strftime("%B", $current) == strftime("%B", $next) && strftime("%Y", $current) == strftime("%Y", $next)){
            $colspan++;
            $border[$i] = false;
        } else {
			$border[$i] = true; // bordure pour distinguer les mois
			if ($_SESSION["langue"]=="EN") {
                echo '<th colspan="'.$colspan.'" class="bg-primary month" id="M'.$current.'">'.date("F",$current).' '.strftime("%Y", $current).'</th>'."\n";
            } else {
                echo '<th colspan="'.$colspan.'" class="bg-primary month" id="M'.$current.'">'.strftime("%B",$current).' '.strftime("%Y", $current).'</th>'."\n";
            }
            $colspan=1;
        }
        $td_headers[$i] = 'M'.$current;
    }
    
    $border[count($border)-1] = false; // suppression de la bordure droite du dernier mois
    
    echo '<th></th>
</tr><tr>
<th role="presentation"></th>'."\n";

    //affichage des jours
    $colspan = 1;
    for ($i = 0; $i < count($toutsujet); $i++) {
        $current = $toutsujet[$i];

        if (strpos($toutsujet[$i], '@') !== false) {
            $current = substr($toutsujet[$i], 0, strpos($toutsujet[$i], '@'));
        }

        if (isset($toutsujet[$i+1]) && strpos($toutsujet[$i+1], '@') !== false) {
            $next = substr($toutsujet[$i+1], 0, strpos($toutsujet[$i+1], '@'));
        } elseif (isset($toutsujet[$i+1])) {
            $next = $toutsujet[$i+1];
        }

        if (isset($toutsujet[$i+1]) && strftime("%a %e",$current)==strftime("%a %e",$next)&&strftime("%B",$current)==strftime("%B",$next)){
            $colspan++;
        } else {
			$rbd = ($border[$i]) ? ' rbd' : '';
            if ($_SESSION["langue"]=="EN") {
                echo '<th colspan="'.$colspan.'" class="bg-primary day'.$rbd.'" id="D'.$current.'">'.date("D jS",$current).'</th>'."\n";
            } else {
                echo '<th colspan="'.$colspan.'" class="bg-primary day'.$rbd.'" id="D'.$current.'">'.strftime("%a %e",$current).'</th>'."\n";
            }
            $colspan=1;
        }
        $td_headers[$i] .= ' D'.$current;
    }

    echo '<th></th>
</tr>'."\n";

    //affichage des horaires
    if (strpos($dsujet->sujet,'@') !== false) {
        echo '<tr>
<th role="presentation"></th>'."\n";

        for ($i = 0; isset($toutsujet[$i]); $i++) {
			$rbd = ($border[$i]) ? ' rbd' : '';
            $heures=explode("@", $toutsujet[$i]);
            if (isset($heures[1])) {
                echo '<th class="bg-info'.$rbd.'" id="H'.preg_replace("/[^a-zA-Z0-9]_+/", "", $heures[0].$heures[1]).'">'.$heures[1].'</th>'."\n";
                $td_headers[$i] .= ' H'.preg_replace("/[^a-zA-Z0-9]_+/", "", $heures[0].$heures[1]);
            } else {
                echo '<th class="bg-info'.$rbd.'"></th>'."\n";
            }
        }

        echo '<th></th>
</tr>
        </thead>
        <tbody>'."\n";

    }
} else {
    $toutsujet=str_replace("°","'",$toutsujet);

    //affichage des sujets du sondage
    echo '<tr>
<th role="presentation"></th>'."\n";

    for ($i = 0; isset($toutsujet[$i]); $i++) {
        echo '<th class="bg-info" id="S'.preg_replace("/[^a-zA-Z0-9]_+/", "", stripslashes($toutsujet[$i])).'">'.stripslashes($toutsujet[$i]).'</th>'."\n";
        $td_headers[$i] .= 'S'.preg_replace("/[^a-zA-Z0-9]_+/", "", stripslashes($toutsujet[$i]));
    }

    echo '<th></th>
</tr>
        </thead>
        <tbody>'."\n";

}

//affichage des resultats
$somme[] = 0;
$compteur = 0;

while ($data = $user_studs->FetchNextObject(false)) {
    
    $ensemblereponses = $data->reponses;
    
    //affichage du nom
    $nombase=str_replace("°","'",$data->nom);
    echo '<tr>
<th class="bg-info">'.stripslashes($nombase).'</th>'."\n";

    //si la ligne n'est pas a changer, on affiche les données
    if (!$testligneamodifier) {
        for ($k = 0; $k < $nbcolonnes; $k++) {
			$rbd = ($border[$k]) ? ' rbd' : '';
            $car = substr($ensemblereponses, $k, 1);
            switch ($car) {
                case "1": echo '<td class="bg-success text-success'.$rbd.'" headers="'.$td_headers[$k].'"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"> ' . _('Yes') . '</span></td>'."\n";
                    if (isset($somme[$k]) === false) {
                        $somme[$k] = 0;
                    }
                    $somme[$k]++; break;
                case "2":  echo '<td class="bg-warning text-warning'.$rbd.'" headers="'.$td_headers[$k].'">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only"> ' . _('Yes') . _(', ifneedbe') . '</span></td>'."\n"; break;
                default: echo '<td class="bg-danger'.$rbd.'" headers="'.$td_headers[$k].'"><span class="sr-only">' . _('No') . '</span></td>'."\n";break;
            }
        }
    } else { //sinon on remplace les choix de l'utilisateur par une ligne de radio pour recuperer de nouvelles valeurs
        //si c'est bien la ligne a modifier on met les radios
        if ($compteur == "$ligneamodifier") {
            for ($j = 0; $j < $nbcolonnes; $j++) {
                
                $car = substr($ensemblereponses, $j, 1);
                
                // variable pour afficher la valeur cochée
                $car_html[0]='value="0"';$car_html[1]='value="1"';$car_html[2]='value="2"';
                switch ($car) { 
                    case "1": $car_html[1]='value="1" checked';break;
                    case "2": $car_html[2]='value="2" checked';break; 
                    default: $car_html[0]='value="0" checked';break;
                }
                    
                echo '<td class="bg-info" headers="'.$td_headers[$j].'">
    <ul class="list-unstyled choice">
        <li class="yes"><input type="radio" id="y-choice-'.$j.'" name="choix'.$j.'" '.$car_html[1].'><label for="y-choice-'.$j.'"> ' . _('Yes') . '</label></li>
        <li class="ifneedbe"><input type="radio" id="i-choice-'.$j.'" name="choix'.$j.'" '.$car_html[2].'><label for="i-choice-'.$j.'"> (' . _('Yes') . '<span class="sr-only">' . _(', ifneedbe') . '</span>)</label></li>
        <li class="no"><input type="radio" id="n-choice-'.$j.'" name="choix'.$j.'" '.$car_html[0].'><label for="n-choice-'.$j.'"> ' . _('No') . '</label></li>
    </ul>
</td>'."\n";
                
            }
        } else { //sinon on affiche les lignes normales
            for ($k = 0; $k < $nbcolonnes; $k++) {
				$rbd = ($border[$k]) ? ' rbd' : '';
                $car = substr($ensemblereponses, $k, 1);
                switch ($car) {
                    case "1": echo '<td class="bg-success text-success'.$rbd.'" headers="'.$td_headers[$k].'"><span class="glyphicon glyphicon-ok"></span><span class="sr-only"> ' . _('Yes') . '</span></td>'."\n";
                        if (isset($somme[$k]) === false) {
                            $somme[$k] = 0;
                        }
                        $somme[$k]++; break;
                    case "2":  echo '<td class="bg-warning text-warning'.$rbd.'" headers="'.$td_headers[$k].'">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only"> ' . _('Yes') . _(', ifneedbe') . '</span></td>'."\n"; break;
                    default: echo '<td class="bg-danger'.$rbd.'" headers="'.$td_headers[$k].'"><span class="sr-only">' . _('No') . '</span></td>'."\n";break;
                }
            }
        }
    }

    //a la fin de chaque ligne se trouve les boutons modifier
    if (!$testligneamodifier=="true") {
        echo '<td><button type="submit" class="btn btn-link btn-sm" name="modifierligne'.$compteur.'" title="'. _('Edit the line:') .' '.stripslashes($nombase).'">
        <span class="glyphicon glyphicon-pencil"></span></button><button type="submit" name="effaceligne'.$compteur.'" title="'. _('Remove the line:') .' '.stripslashes($nombase).'" class="btn btn-link btn-sm"><span class="glyphicon glyphicon-remove text-danger"></span></button></td>'."\n";
    }

    //demande de confirmation pour modification de ligne
    for ($i = 0; $i < $nblignes; $i++) {
        if (isset($_POST["modifierligne$i"])) {
            if ($compteur == $i) {
                echo '<td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="validermodifier'.$compteur.'" title="'. _('Save the choices:') .' '.stripslashes($nombase).'">'. _('Save') .'</button></td>'."\n";
            }
        }
    }

    $compteur++;
    echo '</tr>'."\n";
}

if (!$testligneamodifier=="true") {
    //affichage de la case vide de texte pour un nouvel utilisateur
    echo '<tr id="vote-form">
<td class="bg-info" style="padding:5px">
    <div class="input-group input-group-sm">
        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
        <input type="text" id="nom" name="nom" class="form-control" title="'. _('Your name') .'" placeholder="'. _('Your name') .'" />
    </div>
</td>'."\n";

    //une ligne de checkbox pour le choix du nouvel utilisateur
    for ($i = 0; $i < $nbcolonnes; $i++) {
        echo '<td class="bg-info" headers="'.$td_headers[$i].'">
    <ul class="list-unstyled choice">
        <li class="yes"><input type="radio" id="y-choice-'.$i.'" name="choix'.$i.'" value="1" ><label for="y-choice-'.$i.'">' . _('Yes') . '</label></li>
        <li class="ifneedbe"><input type="radio" id="i-choice-'.$i.'" name="choix'.$i.'" value="2"><label for="i-choice-'.$i.'">(' . _('Yes') . '<span class="sr-only">, ' . _('ifneedbe') . '</span>)</label></li>
        <li class="no"><input type="radio" id="n-choice-'.$i.'" name="choix'.$i.'" value="0" checked><label for="n-choice-'.$i.'">' . _('No') . '</label></li>
    </ul>
</td>'."\n";
    }

    // Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
    echo '<td><button type="submit" class="btn btn-success btn-sm" name="boutonp" title="'. _('Save my choices') .'">'. _('Save') .'</button></td>
</tr>'."\n";

    //focus en javascript sur le champ texte pour le nom d'utilisateur
    echo '<script type="text/javascript">document.formulaire.nom.focus();</script>'."\n";

}

//determination du meilleur choix
for ($i = 0; $i < $nbcolonnes + 1; $i++) {
    if (isset($somme[$i]) === true) {
        if ($i == "0") {
            $meilleurecolonne = $somme[$i];
        }

        if (isset($somme[$i]) && $somme[$i] > $meilleurecolonne){
            $meilleurecolonne = $somme[$i];
        }
    }
}


//affichage de la ligne contenant les sommes de chaque colonne
echo '<tr>
<td align="right">'. _("Addition") .'</td>'."\n";

for ($i = 0; $i < $nbcolonnes; $i++) {
    if (isset($somme[$i]) === true) {
        $affichesomme = $somme[$i];
    } else {
        $affichesomme = '';
    }

    if ($affichesomme == "") {
        $affichesomme = "0";
    }

    echo '<td>'.$affichesomme.'</td>'."\n";
    
}
echo '<td></td>
</tr><tr>
<td></td>'."\n";

for ($i = 0; $i < $nbcolonnes; $i++) {
    if (isset($somme[$i]) === true && isset($meilleurecolonne) === true && $somme[$i] == $meilleurecolonne){
        echo '<td><span class="glyphicon glyphicon-star text-warning"></span></td>'."\n";
    } else {
        echo '<td></td>'."\n";
    }
}
echo '<td></td>
</tr>'."\n";


// S'il a oublié de remplir un nom
if ((isset($_POST["boutonp"])) && $_POST["nom"] == "") {
    echo '<tr><td colspan="10"><p class="text-danger">' . _("Enter a name !") . '</p></tr>'."\n";
}

if (isset($erreur_prenom) && $erreur_prenom) {
  echo '<tr><td colspan="10"><p class="text-danger">' . _("The name you've chosen already exist in this poll!") . '</p></td></tr>'."\n";
}

if (isset($erreur_injection) && $erreur_injection) {
  echo '<tr><td colspan="10"><p class="text-danger">' . _("Characters \"  '  < et > are not permitted") . '</p></td></tr>'."\n";
}

if (isset($erreur_ajout_date) && $erreur_ajout_date) {
  echo '<tr><td colspan="10"><p class="text-danger">' . _("The date is not correct !") . '</p></td></tr>'."\n";
}

//fin du tableau
echo '
        </tbody>
    </table>
    </div>'."\n";

//recuperation des valeurs des sujets et adaptation pour affichage
$toutsujet = explode(",", $dsujet->sujet);

//recuperation des sujets des meilleures colonnes
$compteursujet = 0;
$meilleursujet = '';
for ($i = 0; $i < $nbcolonnes; $i++) {
    if (isset($somme[$i]) === true && isset($meilleurecolonne) === true && $somme[$i] == $meilleurecolonne){
        $meilleursujet.=", ";

        if ($dsondage->format == "D" || $dsondage->format == "D+") {
            $meilleursujetexport = $toutsujet[$i];

            if (strpos($toutsujet[$i], '@') !== false) {
                $toutsujetdate = explode("@", $toutsujet[$i]);
                if ($_SESSION["langue"] == "EN") {
                    $meilleursujet .= date("l, F jS Y",$toutsujetdate[0])." " . _("for") ." ".$toutsujetdate[1];
                } else {
                    $meilleursujet .= strftime(_("%A, den %e. %B %Y"),$toutsujetdate[0]). ' ' . _("for")  . ' ' . $toutsujetdate[1];
                }
            } else {
                if ($_SESSION["langue"] == "EN") {
                    $meilleursujet .= date("l, F jS Y",$toutsujet[$i]);
                } else {
                    $meilleursujet .= strftime(_("%A, den %e. %B %Y"),$toutsujet[$i]);
                }
            }
        } else {
            $meilleursujet.=$toutsujet[$i];
        }
        $compteursujet++;
    }
}

//adaptation pour affichage des valeurs
$meilleursujet = substr("$meilleursujet", 1);
$meilleursujet = str_replace("°", "'", $meilleursujet);
$vote_str = (isset($meilleurecolonne) && $meilleurecolonne > 1) ? $vote_str = _('votes') : _('vote');

echo '<p class="affichageresultats">'."\n";

//affichage de la phrase annoncant le meilleur sujet
if (isset($meilleurecolonne) && $compteursujet == "1") {
    echo '<span class="glyphicon glyphicon-star text-warning"></span> ' . _("The best choice at this time is") . ' : <b>' . $meilleursujet . ' </b>' . _("with") . ' <b>' . $meilleurecolonne . '</b> ' . $vote_str . ".\n";
} elseif (isset($meilleurecolonne)) {
    echo '<span class="glyphicon glyphicon-star text-warning"></span> ' . _("The bests choices at this time are") . ' : <b>' . $meilleursujet . ' </b>' . _("with") . ' <b>' . $meilleurecolonne . '</b> ' . $vote_str . ".\n";
}

echo '</p>
</form>
<form name="formulaire4" action="#bas" method="POST">'."\n";
//Gestion du sondage
echo '
    <hr />
    <div class="row">
    <fieldset id="poll-management" class="col-md-6 col-md-offset-3"><legend>'. _("Poll's management") .' :</legend>'."\n";

//Changer le titre du sondage
$adresseadmin=$dsondage->mail_admin;
echo '
        <p><label for="nouveautitre">'. _("Poll title: ") .'</label></p>
        <p><input type="text" title="'. _("Change the title") .'" id="nouveautitre" name="nouveautitre" size="40" value="'.$dsondage->titre.'" /></p>
        <p class="text-right"><input type="submit" name="boutonnouveautitre" value="'. _('Save the new title') .'" class="btn btn-success btn-xs" /></p>'."\n";

//si la valeur du nouveau titre est invalide : message d'erreur
if ((isset($_POST["boutonnouveautitre"])) && !issetAndNoEmpty('nouveautitre')) {
    echo '<p class="text-danger">'. _("Enter a new title!") .'</p>'."\n"; // /!\ manque aria-describeby
}

//Changer l'adresse de l'administrateur
echo '
        <p><label for="nouvelleadresse">'. _("Your e-mail address: ") .'</label></p>
        <p><input type="text" title="'. _("Change your email") .'" id="nouvelleadresse" name="nouvelleadresse" size="40" value="'.$dsondage->mail_admin.'" /></p>
        <p class="text-right"><input type="submit" name="boutonnouvelleadresse" value="'. _('Save your new email') .'" class="btn btn-success btn-xs" /></p>'."\n";

//si l'adresse est invalide ou le champ vide : message d'erreur
if ((isset($_POST["boutonnouvelleadresse"])) && !issetAndNoEmpty('nouvelleadresse')) {
    echo '<p class="text-danger">'. _("Enter a new email address!") .'</p>'."\n"; // /!\ manque aria-describeby
}

//Changer la description du sondage
echo '
        <p><label for="nouveauxcommentaires">'. _("Description: ") .'</label></p>
        <p><textarea title="'. _("Change the description") .'" id="nouveauxcommentaires" name="nouveauxcommentaires" rows="7" cols="40">'.stripslashes($dsondage->commentaires).'</textarea></p>
        <p class="text-right"><input type="submit" name="boutonnouveauxcommentaires" value="'. _("Save the description") .'"  class="btn btn-success btn-xs" /></p>
    </fieldset>
</div>'."\n";

//affichage des commentaires des utilisateurs existants
$sql = 'SELECT * FROM comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
$sql = $connect->Prepare($sql);
$comment_user = $connect->Execute($sql, array($numsondage));

echo '
    <hr />
    <div class="row">';

if ($comment_user->RecordCount() != 0) {
    echo '<div class="col-md-7"><h3>' . _("Comments of polled people") . ' :</h3>'."\n";

    $i = 0;
    while ( $dcomment=$comment_user->FetchNextObject(false)) {
        echo '
    <div class="comment">
        <button type="submit" name="suppressioncomment'.$i.'" class="btn btn-link" title="' . _('Remove the comment') . '"><span class="glyphicon glyphicon-remove text-danger"></span></button>
        <b>'.stripslashes($dcomment->usercomment). ' :</b>
        <span class="comment">' . stripslashes(nl2br($dcomment->comment)) . '</span>
    </div>';
        $i++;
    }
    echo '</div>
        <div class="col-md-5 hidden-print">';
} else {
    echo '
        <div class="col-md-6 col-md-offset-3 hidden-print">';
}

if (isset($erreur_commentaire_vide) && $erreur_commentaire_vide=="yes") {
    echo '<p class="text-danger">' . _("Enter a name and a comment!") . '</p>';
}

//affichage de la case permettant de rajouter un commentaire par les utilisateurs
echo '
            <div class="alert alert-info">
            <fieldset id="add-comment"><legend>' . _("Add a comment in the poll:") . '</legend>
                <div class="form-group">
                    <p><label for="commentuser">'. _("Name") .'</label> : <input type=text name="commentuser" class="form-control" id="commentuser" /></p>
                </div>
                <div class="form-group">
                    <p><label for="comment">'. _("Your comment: ") .'</label><br />
                    <textarea title="'. _("Write your comment") .'" name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea></p>
                </div>
                <p class="text-center"><input type="submit" name="ajoutcomment" value="'. _("Send your comment") .'" class="btn btn-success"></p>
            </fieldset>
            </div>
        </div>
    </div>
</form>

<a id="bas"></a>';

//fin de la partie GESTION et bandeau de pied
bandeau_pied();
