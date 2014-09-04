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

//setlocale(LC_TIME, "fr_FR");
include_once __DIR__ . '/app/inc/init.php';

if (file_exists('bandeaux_local.php')) {
    include_once('bandeaux_local.php');
} else {
    include_once('bandeaux.php');
}

// Initialisation des variables
$numsondageadmin = false;
$sondage = false;

// recuperation du numero de sondage admin (24 car.) dans l'URL
if (Utils::issetAndNoEmpty('sondage', $_GET) && is_string($_GET['sondage']) && strlen($_GET['sondage']) === 24) {
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
    Utils::print_header( _("Error!"));

    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alert-warning">
        <h2>' . _("This poll doesn't exist !") . '</h2>
        <p>' . _('Back to the homepage of ') . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    bandeau_pied();

    die();
}

$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);


//si la valeur du nouveau titre est valide et que le bouton est activé
$adresseadmin = $dsondage->mail_admin;

if (isset($_POST["boutonnouveautitre"])) {
    if (Utils::issetAndNoEmpty('nouveautitre') === false) {
        $err |= TITLE_EMPTY;
    } else {
        //modification de la base SQL avec le nouveau titre
        $nouveautitre = htmlentities(html_entity_decode($_POST['nouveautitre'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
        $sql = 'UPDATE sondage SET titre = '.$connect->Param('nouveautitre').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        //envoi du mail pour prevenir l'admin de sondage
        if ($connect->Execute($sql, array($nouveautitre, $numsondage))) {
            Utils::sendEmail( $adresseadmin,
                _("[ADMINISTRATOR] New title for your poll") . ' ' . NOMAPPLICATION,
                _("You have changed the title of your poll. \nYou can modify this poll with this link") .
                " :\n\n" . Utils::getUrlSondage($numsondageadmin, true) . "\n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

// si le bouton est activé, quelque soit la valeur du champ textarea
if (isset($_POST["boutonnouveauxcommentaires"])) {
    if (Utils::issetAndNoEmpty('nouveautitre') === false) {
        $err |= COMMENT_EMPTY;
    } else {
        $commentaires = htmlentities(html_entity_decode($_POST['nouveauxcommentaires'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        //modification de la base SQL avec les nouveaux commentaires
        $sql = 'UPDATE sondage SET commentaires = '.$connect->Param('commentaires').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        if ($connect->Execute($sql, array($commentaires, $numsondage))) {
            //envoi du mail pour prevenir l'admin de sondage
            Utils::sendEmail( $adresseadmin,
                _("[ADMINISTRATOR] New description of your poll") . ' ' . NOMAPPLICATION,
                _("You have changed the description of your poll. \nYou can modify this poll with this link") .
                " :\n\n" . Utils::getUrlSondage($numsondageadmin, true) . " \n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

//si la valeur de la nouvelle adresse est valide et que le bouton est activé
if (isset($_POST["boutonnouvelleadresse"])) {
    if (Utils::issetAndNoEmpty('nouvelleadresse') === false || Utils::isValidEmail($_POST["nouvelleadresse"]) === false) {
       $err |= INVALID_EMAIL;
    } else {
        $nouvelleadresse = htmlentities(html_entity_decode($_POST['nouvelleadresse'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        //modification de la base SQL avec la nouvelle adresse
        $sql = 'UPDATE sondage SET mail_admin = '.$connect->Param('nouvelleadresse').' WHERE id_sondage = '.$connect->Param('numsondage');
        $sql = $connect->Prepare($sql);

        if ($connect->Execute($sql, array($nouvelleadresse, $numsondage))) {
            //envoi du mail pour prevenir l'admin de sondage
            Utils::sendEmail( $_POST['nouvelleadresse'],
                _("[ADMINISTRATOR] New email address for your poll") . ' ' . NOMAPPLICATION,
                _("You have changed your email address in your poll. \nYou can modify this poll with this link") .
                " :\n\n" . Utils::getUrlSondage($numsondageadmin, true) . "\n\n" .
                _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );
        }
    }
}

// reload
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

if (isset($_POST["ajoutsujet"])) {
    Utils::print_header('');

    bandeau_titre(_("Make your polls"));

    //on recupere les données et les sujets du sondage

    echo '
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
            <form name="formulaire" class="form-horizontal" action="' . Utils::getUrlSondage($numsondageadmin, true) . '" method="POST">
                <h2>' . _("Column's adding") . '</h2>'."\n";

    if ($dsondage->format=="A"||$dsondage->format=="A+"){
        echo '
                <div class="form-group">
                    <label for="nouvellecolonne" class="col-md-6">' . _("Add a column") .' :</label>
                    <div class="col-md-6">
                        <input type="text" id="nouvellecolonne" name="nouvellecolonne" class="form-control" />
                    </div>
                </div>'."\n";
    } else {
        //ajout d'une date avec creneau horaire
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

if (isset($_POST["suppressionsondage"])) {
    Utils::print_header('');

    bandeau_titre(_("Confirm removal of your poll"));

    echo '
        <form name="formulaire" action="' . Utils::getUrlSondage($numsondageadmin, true) . '" method="POST">
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

    if (Utils::remove_sondage($connect, $numsondage)) {
        // on ecrit dans le fichier de logs la suppression du sondage
        error_log($date . " SUPPRESSION: $dsondage->id_sondage\t$dsondage->format\t$dsondage->nom_admin\t$dsondage->mail_admin\n", 3, 'admin/logs_studs.txt');

        //envoi du mail a l'administrateur du sondage
        Utils::sendEmail( $adresseadmin,
            _("[ADMINISTRATOR] Removing of your poll") . ' ' . NOMAPPLICATION,
            _("You have removed your poll. \nYou can make new polls with this link") .
            " :\n\n" . Utils::get_server_name() . "index.php \n\n" .
            _("Thanks for your confidence.") . "\n" . NOMAPPLICATION );

        //affichage de l'ecran de confirmation de suppression de sondage
        Utils::print_header('');

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

// quand on ajoute un commentaire utilisateur
if (isset($_POST['ajoutcomment'])) {
    if (Utils::issetAndNoEmpty('commentuser') === false) {
        $err |= COMMENT_USER_EMPTY;
    } else {
        $comment_user = htmlentities(html_entity_decode($_POST["commentuser"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');
    }

    if(Utils::issetAndNoEmpty('comment') === false) {
        $err |= COMMENT_EMPTY;
    }

    if (Utils::issetAndNoEmpty('comment') && !Utils::is_error(COMMENT_EMPTY) && !Utils::is_error(NO_POLL) && !Utils::is_error(COMMENT_USER_EMPTY)) {
        $comment = htmlentities(html_entity_decode($_POST["comment"], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

        // Check for doublons
        $comment_doublon = false;
        $req = 'SELECT * FROM comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
        $sql = $connect->Prepare($req);
        $comment_user_doublon = $connect->Execute($sql, array($numsondage));
        if ($comment_user_doublon->RecordCount() != 0) {
            while ( $dcomment_user_doublon=$comment_user_doublon->FetchNextObject(false)) {
                if($dcomment_user_doublon->comment == $comment && $dcomment_user_doublon->usercomment == $comment_user) {
                    $comment_doublon = true;
                };
            }
        }

        if(!$comment_doublon) {
            $req = 'INSERT INTO comments (id_sondage, comment, usercomment) VALUES ('.
                $connect->Param('id_sondage').','.
                $connect->Param('comment').','.
                $connect->Param('comment_user').')';
            $sql = $connect->Prepare($req);

            $comments = $connect->Execute($sql, array($numsondage, $comment, $comment_user));
            if ($comments === false) {
                $err |= COMMENT_INSERT_FAILED;
            }
        }
    }
}

$nbcolonnes = substr_count($dsujet->sujet, ',') + 1;
$nblignes = $user_studs->RecordCount();

//si il n'y a pas suppression alors on peut afficher normalement le tableau

//action si le bouton participer est cliqué
if (isset($_POST["boutonp"])) {
    //si on a un nom dans la case texte
    if (Utils::issetAndNoEmpty('nom')){
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
if (isset($_POST["ajoutercolonne"]) && Utils::issetAndNoEmpty('nouvellecolonne') && ($dsondage->format == "A" || $dsondage->format == "A+")) {
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
      Utils::sendEmail( "$adresseadmin", "" . _("[ADMINISTRATOR] New column for your poll").NOMAPPLICATION, "" .
          _("You have added a new column in your poll. \nYou can inform the voters of this change with this link") .
          " : \n\n" . Utils::getUrlSondage($numsondage) . " \n\n " . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
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

        Utils::sendEmail( $adresseadmin,
            _("[ADMINISTRATOR] New column for your poll"),
            _("You have added a new column in your poll. \nYou can inform the voters of this change with this link").
            " : \n\n" . Utils::getUrlSondage($numsondage) . " \n\n " . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );

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

    Utils::print_header(_("Error!"));
    bandeau_titre(_("Error!"));

    echo '
    <div class="alert alert-warning">
        <h2>' . _("This poll doesn't exist !") . '</h2>
        <p>' . _('Back to the homepage of ') . ' <a href="' . Utils::get_server_name() . '"> ' . NOMAPPLICATION . '</a></p>
    </div>'."\n";

    bandeau_pied();

    die();
}

// Errors
$errors = '';
if ((isset($_POST["boutonp"])) && $_POST["nom"] == "") {
    $errors .= '<li>' . _("Enter a name") . '</li>';
}
if (isset($erreur_prenom) && $erreur_prenom) {
    $errors .= '<li>' . _("The name you've chosen already exist in this poll!") . '</li>';
}
if (isset($erreur_injection) && $erreur_injection) {
    $errors .= '<li>' . _("Characters \"  '  < et > are not permitted") . '</li>';
}
if (isset($erreur_ajout_date) && $erreur_ajout_date) {
    $errors .= '<li>' . _("The date is not correct !") . '</li>';
}

if ($errors!='') {
    Utils::print_header(_("Error!"));
    bandeau_titre(_("Error!"));

    echo '<div class="alert alert-danger"><ul class="list-unstyled">'.$errors.'</ul></div>'."\n";

} else {
    Utils::print_header('');
    bandeau_titre(_("Make your polls"));

   // session_unset();
}

//Poll title, description and email values
$title = (isset($_POST["boutonnouveautitre"]) && Utils::issetAndNoEmpty('nouveautitre')) ? htmlentities(html_entity_decode($_POST['nouveautitre'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') : stripslashes( $dsondage->titre );
$description = (isset($_POST["nouveauxcommentaires"])) ? stripslashes(htmlentities(html_entity_decode($_POST['nouveauxcommentaires'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8')) : stripslashes( $dsondage->commentaires );
$email_admin = (isset($_POST["boutonnouvelleadresse"]) && Utils::issetAndNoEmpty('nouvelleadresse')) ? htmlentities(html_entity_decode($_POST['nouvelleadresse'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') : stripslashes( $dsondage->mail_admin );

echo '
    <form name="formulaire4" action="' . Utils::getUrlSondage($numsondageadmin, true) . '" method="POST">
        <div class="jumbotron">
            <div class="row">
                <div class="col-md-7" id="title-form">
                    <h2>'.$title.'<button class="btn btn-link btn-sm btn-edit" title="'. _('Edit the title') .'"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _('Edit') . '</span></button></h2>
                    <div class="hidden js-title">
                        <label class="sr-only" for="newtitle">'. _("Title") .'</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newtitle" name="nouveautitre" size="40" value="'.$title.'" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success" name="boutonnouveautitre" value="1" title="'. _('Save the new title') .'"><span class="glyphicon glyphicon-ok"></span></button>
                                <button class="btn btn-link btn-cancel" title="'. _('Cancel the title edit') .'"><span class="glyphicon glyphicon-remove"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="btn-group pull-right">
                        <button onclick="javascript:print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> ' . _('Print') . '</button>
                        <button onclick="window.location.href=\'' . Utils::get_server_name() . 'exportcsv.php?numsondage=' . $numsondage . '\';return false;" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> ' . _('Export to CSV') . '</button>
                        <button type="submit" id="suppressionsondage" name="suppressionsondage" value="" class="btn btn-danger" title="'. _("Remove the poll") .'"><span class="glyphicon glyphicon-trash"></span></button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group" >
                        <div id="author-form">
                            <label class="control-label">'. _("Initiator of the poll") .'</label>
                            <p> '.stripslashes($dsondage->nom_admin).'</p>
                        </div>
                        <div id="email-form">
                            <p>'.$email_admin.'<button class="btn btn-link btn-sm btn-edit" title="'. _('Edit the email adress') .'"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _('Edit') . '</span></button></p>
                            <div class="hidden js-email">
                                <label class="sr-only" for="newemail">'. _("Email adress") .'</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="newemail" name="nouvelleadresse" size="40" value="'.$email_admin.'" />
                                    <span class="input-group-btn">
                                        <button type="submit" name="boutonnouvelleadresse" value="1" class="btn btn-success" title="'. _('Save the adress email') .'"><span class="glyphicon glyphicon-ok"></span></button>
                                        <button class="btn btn-link btn-cancel" title="'. _('Cancel the adress email edit') .'"><span class="glyphicon glyphicon-remove"></span></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-7" id="description-form">
                    <label for="newdescription">'._("Description") .'</label><button class="btn btn-link btn-sm btn-edit" title="'. _('Edit the description') .'"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _('Edit') . '</span></button><br />
                    <p class="well">'.$description.'</p>
                    <div class="hidden js-desc text-right">
                        <textarea class="form-control" id="nouveauxcommentaires" name="nouveauxcommentaires" rows="2" cols="40">'.$description.'</textarea>
                        <button type="submit" id="btn-new-desc" name="boutonnouveauxcommentaires" value="1" class="btn btn-sm btn-success" title="'. _("Save the description") .'">'. _("Save") .'</button>
                        <button class="btn btn-default btn-sm btn-cancel" title="'. _('Cancel the description edit') .'">'. _('Cancel') .'</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-5">
                    <label for="public-link">'._("Public link of the pool") .' <a href="' . Utils::getUrlSondage($dsondage->id_sondage) . '" class="glyphicon glyphicon-link"></a></label>
                    <input class="form-control" id="public-link" type="text" readonly="readonly" value="' . Utils::getUrlSondage($dsondage->id_sondage) . '" />
                </div>
                <div class="form-group col-md-5">
                    <label for="admin-link">'._("Admin link of the pool") .' <a href="' . Utils::getUrlSondage($numsondageadmin, true) . '" class="glyphicon glyphicon-link"></a></label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="' . Utils::getUrlSondage($numsondageadmin, true) . '" />
                </div>
                <div class="form-group col-md-2">
                    <label>'. _("Expiration's date") .'</label>
                    <p>'.date("d/m/Y",strtotime($dsondage->date_fin)).'</p>
                </div>
            </div>
        </div>
    </form>'."\n"; // .jumbotron

//on recupere les données et les sujets du sondage
$dsujet=$sujets->FetchObject(false);
$dsondage=$sondage->FetchObject(false);

//reformatage des données des sujets du sondage
$toutsujet=explode(",",$dsujet->sujet);
$toutsujet=str_replace("°","'",$toutsujet);
$nbcolonnes=substr_count($dsujet->sujet,',')+1;

// Table headers
$thead = '<thead>';

// Button in the first td to avoid remove col on "Return" keypress)
$tr_add_remove_col = '<tr><td role="presentation"><button type="submit" class="invisible" name="boutonp" ></button></td>';

$border = array(); // bordure pour distinguer les mois
$td_headers = array(); // for a11y, headers="M1 D4 H5" on each td
$radio_title = array(); // date for

// Dates poll
if ($dsondage->format=="D"||$dsondage->format=="D+") {

    $tr_months = '<tr><th role="presentation"></th>';
    $tr_days = '<tr><th role="presentation"></th>';
    $tr_hours = '<tr><th role="presentation"></th>';

    // Headers
    $colspan_month = 1;
    $colspan_day = 1;

    for ($i = 0; $i < count($toutsujet); $i++) {

        $border[$i] = false;
        $radio_title[$i] = strftime("%A %e %B %Y",$current);

        // Current date
        $current = $toutsujet[$i];

        // Months
        $td_headers[$i] = 'M'.($i+1-$colspan_month);

        if (isset($toutsujet[$i+1]) && strftime("%B", $current) == strftime("%B", $toutsujet[$i+1]) && strftime("%Y", $current) == strftime("%Y", $toutsujet[$i+1])){
            $colspan_month++;
        } else {
            $border[$i] = true;
            $tr_months .= '<th colspan="'.$colspan_month.'" class="bg-primary month" id="M'.($i+1-$colspan_month).'">'.strftime("%B",$current).' '.strftime("%Y", $current).'</th>';
            $colspan_month=1;
        }

        // Days
        $td_headers[$i] .= ' D'.($i+1-$colspan_day);

        if (isset($toutsujet[$i+1]) && strftime("%a %e",$current)==strftime("%a %e",$toutsujet[$i+1]) && strftime("%B",$current)==strftime("%B",$toutsujet[$i+1])){
            $colspan_day++;
        } else {
            $rbd = ($border[$i]) ? ' rbd' : '';
            $tr_days .= '<th colspan="'.$colspan_day.'" class="bg-primary day'.$rbd.'" id="D'.($i+1-$colspan_day).'">'.strftime("%a %e",$current).'</th>';
            $colspan_day=1;
        }

        // Hours
        if (strpos($dsujet->sujet,'@') !== false) {
            $rbd = ($border[$i]) ? ' rbd' : '';
            $hour = substr($toutsujet[$i], strpos($toutsujet[$i], '@')-count($toutsujet[$i])+2);

            if ($hour != "") {
                $tr_hours .= '<th class="bg-info'.$rbd.'" id="H'.$i.'">'.$hour.'</th>';
                $radio_title[$i] .= ' - '.$hour;
                $td_headers[$i] .= ' H'.$i;
            } else {
                $tr_hours .= '<th class="bg-info'.$rbd.'"></th>';
            }
        }

        // Remove col
        $tr_add_remove_col .= '<td headers="'.$td_headers[$i].'"><button type="submit" name="effacecolonne'.$i.'" class="btn btn-link btn-sm" title="' . _('Remove the column') . ' ' .$radio_title[$i]. '"><span class="glyphicon glyphicon-remove text-danger"></span></button></td>';

    }

    $border[count($border)-1] = false; // suppression de la bordure droite du dernier mois

    $tr_months .= '<th></th></tr>';
    $tr_days .= '<th></th></tr>';
    $tr_hours .= '<th></th></tr>';

    // Add col
    $tr_add_remove_col .= '<td><button type="submit" name="ajoutsujet" class="btn btn-link btn-sm" title="'. _('Add a column') . '"><span class="glyphicon glyphicon-plus text-success"></span></button></td></tr>';

    $thead = "\n".$tr_add_remove_col."\n".$tr_months."\n".$tr_days."\n".$tr_hours."\n";

// Subjects poll
} else {
    $toutsujet=str_replace("@","<br />",$toutsujet);

    $tr_subjects = '<tr><th role="presentation"></th>';

    for ($i = 0; isset($toutsujet[$i]); $i++) {

        $td_headers[$i]='';$radio_title[$i]=''; // init before concatenate

        // Subjects
        $tr_subjects .= '<th class="bg-info" id="S'.preg_replace("/[^a-zA-Z0-9]_+/", "", stripslashes($toutsujet[$i])).'">'.stripslashes($toutsujet[$i]).'</th>';

        $border[$i] = false;
        $td_headers[$i] .= stripslashes($toutsujet[$i]);
        $radio_title[$i] .= stripslashes($toutsujet[$i]);

        // Remove col
        $tr_add_remove_col .= '<td headers="'.$td_headers[$i].'"><button type="submit" name="effacecolonne'.$i.'" class="btn btn-link btn-sm" title="' . _('Remove the column') . ' '. $radio_title[$i] .'"><span class="glyphicon glyphicon-remove text-danger"></span></button></td>';
    }

    // Add col
    $tr_add_remove_col .= '<td><button type="submit" name="ajoutsujet" class="btn btn-link btn-sm" title="'. _('Add a column') . '"><span class="glyphicon glyphicon-plus text-success"></span></button></td></tr>';

    $thead = $tr_add_remove_col.$tr_subjects.'<th></th></tr>';
}

// Print headers
echo '
<form name="formulaire" action="' . Utils::getUrlSondage($numsondageadmin, true) . '" method="POST">

    <div class="alert alert-info">
        <p>' . _('As poll administrator, you can change all the lines of this poll with this button ').'<span class="glyphicon glyphicon-pencil"></span><span class="sr-only">' . _('Edit') . '</span>,
           ' . _(' remove a column or a line with ') . '<span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">' . _('Remove') . '</span>
           ' . _('and add a new column with '). '<span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">'. _('Add a column') . '</span></p>
        <p>' . _('Finally, you can change the informations of this poll like the title, the comments or your email address.') . '</p>
    </div>
    <div id="tableContainer" class="tableContainer">
    <table class="results">
        <thead>'. $thead . '</thead>
        <tbody>';

// Print poll results
$somme[] = 0;
$compteur = 0;

while ($data = $user_studs->FetchNextObject(false)) {

    $ensemblereponses = $data->reponses;

    // Print name
    $nombase=str_replace("°","'",$data->nom);
    echo '<tr>
<th class="bg-info">'.stripslashes($nombase).'</th>'."\n";

    // si la ligne n'est pas a changer, on affiche les données
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
    } else { // sinon on remplace les choix de l'utilisateur par une ligne de radio pour recuperer de nouvelles valeurs
        // si c'est bien la ligne a modifier on met les radios
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

                echo '
                <td class="bg-info" headers="'.$td_headers[$j].'">
                    <ul class="list-unstyled choice">
                        <li class="yes">
                            <input type="radio" id="y-choice-'.$j.'" name="choix'.$j.'" '.$car_html[1].' />
                            <label class="btn btn-default btn-xs" for="y-choice-'.$j.'" title="' . _('Vote "yes" for ') . $radio_title[$j] . '">
                                <span class="glyphicon glyphicon-ok"></span><span class="sr-only">' . _('Yes') . '</span>
                            </label>
                        </li>
                        <li class="ifneedbe">
                            <input type="radio" id="i-choice-'.$j.'" name="choix'.$j.'" '.$car_html[2].' />
                            <label class="btn btn-default btn-xs" for="i-choice-'.$j.'" title="' . _('Vote "ifneedbe" for ') . $radio_title[$j] . '">
                                (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">' . _('Ifneedbe') . '</span>
                            </label>
                        </li>
                        <li class="no">
                            <input type="radio" id="n-choice-'.$j.'" name="choix'.$j.'" '.$car_html[0].'/>
                            <label class="btn btn-default btn-xs" for="n-choice-'.$j.'" title="' . _('Vote "no" for ') . $radio_title[$j] . '">
                                <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">' . _('No') . '</span>
                            </label>
                        </li>
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
        echo '
        <td class="bg-info" headers="'.$td_headers[$i].'">
            <ul class="list-unstyled choice">
                <li class="yes">
                    <input type="radio" id="y-choice-'.$i.'" name="choix'.$i.'" value="1" />
                    <label class="btn btn-default btn-xs" for="y-choice-'.$i.'" title="' . _('Vote "yes" for ') . $radio_title[$i] . '">
                        <span class="glyphicon glyphicon-ok"></span><span class="sr-only">' . _('Yes') . '</span>
                    </label>
                </li>
                <li class="ifneedbe">
                    <input type="radio" id="i-choice-'.$i.'" name="choix'.$i.'" value="2" />
                    <label class="btn btn-default btn-xs" for="i-choice-'.$i.'" title="' . _('Vote "ifneedbe" for ') . $radio_title[$i] . '">
                        (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">' . _('Ifneedbe') . '</span>
                    </label>
                </li>
                <li class="no">
                    <input type="radio" id="n-choice-'.$i.'" name="choix'.$i.'" value="0" checked/>
                    <label class="btn btn-default btn-xs" for="n-choice-'.$i.'" title="' . _('Vote "no" for ') . $radio_title[$i] . '">
                        <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">' . _('No') . '</span>
                    </label>
                </li>
            </ul>
        </td>'."\n";
    }

    // Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
    echo '<td><button type="submit" class="btn btn-success btn-sm" name="boutonp" title="'. _('Save the choices') .'">'. _('Save') .'</button></td>
</tr>'."\n";

}

// Addition and Best choice
//affichage de la ligne contenant les sommes de chaque colonne
$tr_addition = '<tr><td align="right">'. _("Addition") .'</td>';
$tr_bestchoice = '<tr><td></td>';
$meilleurecolonne = 0;

for ($i = 0; $i < $nbcolonnes; $i++) {
    if (isset($somme[$i]) && $somme[$i] > 0 ) {
        if (isset($somme[$i]) && $somme[$i] > $meilleurecolonne){
            $meilleurecolonne = $somme[$i];
        }
        $tr_addition .= '<td>'.$somme[$i].'</td>';
    } else {
        $tr_addition .= '<td></td>';
    }
}
$tr_addition .= '<td></td></tr>';

//recuperation des valeurs des sujets et adaptation pour affichage
$toutsujet = explode(",", $dsujet->sujet);

$compteursujet = 0;
$meilleursujet = '';
for ($i = 0; $i < $nbcolonnes; $i++) {

    if (isset($somme[$i]) && $somme[$i] > 0 && $somme[$i] == $meilleurecolonne){
        $tr_bestchoice .= '<td><span class="glyphicon glyphicon-star text-warning"></span></td>';

        $meilleursujet .= ', ';

        if ($dsondage->format == "D" || $dsondage->format == "D+") {
            $meilleursujetexport = $toutsujet[$i];

            if (strpos($toutsujet[$i], '@') !== false) {
                $toutsujetdate = explode("@", $toutsujet[$i]);
                $meilleursujet .= strftime(_("%A, den %e. %B %Y"),$toutsujetdate[0]). ' - ' . $toutsujetdate[1];
            } else {
                $meilleursujet .= strftime(_("%A, den %e. %B %Y"),$toutsujet[$i]);
            }
        } else {
            $meilleursujet.=$toutsujet[$i];
        }
        $compteursujet++;

    } else {
        $tr_bestchoice .= '<td></td>';
    }
}
$tr_bestchoice .= '<td></td></tr>';

$meilleursujet = str_replace("°", "'", substr("$meilleursujet", 1));
$vote_str = ($meilleurecolonne > 1) ? $vote_str = _('votes') : _('vote');

// Print Addition and Best choice
echo $tr_addition."\n".$tr_bestchoice.'
        </tbody>
    </table>
    </div>
    <p class="affichageresultats">'."\n";

if ($compteursujet == 1) {
    echo '<span class="glyphicon glyphicon-star text-warning"></span> ' . _("The best choice at this time is:") . ' <b>' . $meilleursujet . ' </b>' . _("with") . ' <b>' . $meilleurecolonne . '</b> ' . $vote_str . ".\n";
} elseif ($compteursujet > 1) {
    echo '<span class="glyphicon glyphicon-star text-warning"></span> ' . _("The bests choices at this time are:") . ' <b>' . $meilleursujet . ' </b>' . _("with") . ' <b>' . $meilleurecolonne . '</b> ' . $vote_str . ".\n";
}

echo '</p>
</form>
<hr />
<form name="formulaire4" action="#bas" method="POST">'."\n";
// Commments
$sql = 'SELECT * FROM comments WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_comment';
$sql = $connect->Prepare($sql);
$comment_user = $connect->Execute($sql, array($numsondage));

if ($comment_user->RecordCount() != 0) {
    echo '<div class="row"><h3>' . _("Comments of polled people") . '</h3>'."\n";

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
    echo '</div>';
}
echo '
    <div class="row hidden-print alert alert-info">
        <div class="col-md-6 col-md-offset-3">
            <fieldset id="add-comment"><legend>' . _("Add a comment in the poll") . '</legend>
                <div class="form-group">
                    <p><label for="commentuser">'. _("Your name") .'</label><input type=text name="commentuser" class="form-control" id="commentuser" /></p>
                </div>
                <div class="form-group">
                    <p><label for="comment">'. _("Your comment") .'</label><br />
                    <textarea name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea></p>
                </div>
                <p class="text-center"><input type="submit" name="ajoutcomment" value="'. _("Send the comment") .'" class="btn btn-success"></p>
            </fieldset>
        </div>
    </div>
</form>

<a id="bas"></a>';

bandeau_pied();
