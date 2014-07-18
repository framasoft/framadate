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

if (file_exists('bandeaux_local.php')) {
  include_once('bandeaux_local.php');
} else {
  include_once('bandeaux.php');
}
include_once('fonctions.php');

// Le fichier studs.php sert a afficher les résultats d'un sondage à un simple utilisateur. 
// C'est également l'interface pour ajouter une valeur à un sondage deja créé.
$numsondage = false;

//On récupère le numéro de sondage par le lien web.
if(issetAndNoEmpty('sondage', $_GET) === true) {
  $numsondage = $_GET["sondage"];
  $_SESSION["numsondage"] = $numsondage;
}

if(issetAndNoEmpty('sondage') === true) {
  $numsondage = $_POST["sondage"];
  $_SESSION["numsondage"] = $numsondage;
} elseif(issetAndNoEmpty('sondage', $_COOKIE) === true) {
  $numsondage = $_COOKIE["sondage"];
} elseif(issetAndNoEmpty('numsondage', $_SESSION) === true) {
  $numsondage = $_SESSION["numsondage"];
}

if ($numsondage !== false) {
  $dsondage = get_sondage_from_id($numsondage);
  if($dsondage === false) {
    $err |= NO_POLL;
  }
} else {
  $err |= NO_POLL_ID;
}

//output a CSV and die()
if(issetAndNoEmpty('export', $_GET) && $dsondage !== false) {
  if($_GET['export'] == 'csv') {
    require_once('exportcsv.php');
  }
  
  if($_GET['export'] == 'ics' && $dsondage->is_date) {
    require_once('exportics.php');
  }
  
  die();
}

// quand on ajoute un commentaire utilisateur
if(isset($_POST['ajoutcomment']) || isset($_POST['ajoutcomment_x'])) {
  if (isset($_SESSION['nom']) && issetAndNoEmpty('commentuser') === false) {
    // Si le nom vient de la session, on le de-htmlentities
    $comment_user = html_entity_decode($_SESSION['nom'], ENT_QUOTES, 'UTF-8');
  } elseif(issetAndNoEmpty('commentuser')) {
    $comment_user = $_POST["commentuser"];
  } elseif(isset($_POST["commentuser"])) {
    $err |= COMMENT_USER_EMPTY;
  } else {
    $comment_user = _('anonyme');
  }
  
  if(issetAndNoEmpty('comment') === false) {
    $err |= COMMENT_EMPTY;
  }

  if (isset($_POST["comment"]) && !is_error(COMMENT_EMPTY) && !is_error(NO_POLL) && !is_error(COMMENT_USER_EMPTY)) {
    // protection contre les XSS : htmlentities
    $comment = htmlentities($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $comment_user = htmlentities($comment_user, ENT_QUOTES, 'UTF-8');
    
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


// Action quand on clique le bouton participer
$sql = 'SELECT * FROM user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($numsondage));

$nbcolonnes = substr_count($dsondage->sujet, ',') + 1;
if (!is_error(NO_POLL) && (isset($_POST["boutonp"]) || isset($_POST["boutonp_x"]))) {
  //Si le nom est bien entré
  if (issetAndNoEmpty('nom') === false) {
    $err |= NAME_EMPTY;
  }
  
  if(!is_error(NAME_EMPTY) && (! ( USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']) ) || $_POST["nom"] == $_SESSION["nom"])) {
    $nouveauchoix = '';
    for ($i=0;$i<$nbcolonnes;$i++) {
      // Si la checkbox est enclenchée alors la valeur est 1
      if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '1') {
        $nouveauchoix.="1";
      } else { // sinon c'est 0
        $nouveauchoix.="0";
      }
    }
    
    $nom=substr($_POST["nom"],0,64);
    
    // protection contre les XSS : htmlentities
    $nom = htmlentities($nom, ENT_QUOTES, 'UTF-8');

    while($user = $user_studs->FetchNextObject(false)) {
      if ($nom == $user->nom) {
        $err |= NAME_TAKEN;
      }
    }

    // Ecriture des choix de l'utilisateur dans la base
    if (!is_error(NAME_TAKEN) && !is_error(NAME_EMPTY)) {
      
      $sql = 'INSERT INTO user_studs (nom,id_sondage,reponses) VALUES ('.
		$connect->Param('nom').', '.
		$connect->Param('numsondage').', '.
		$connect->Param('nouveauchoix').')';
      $sql = $connect->Prepare($sql);
      
      // Todo : Il faudrait lever une erreur en cas d'erreur d'insertion
      $connect->Execute($sql, array($nom, $numsondage, $nouveauchoix));

      if ($dsondage->mailsonde || /* compatibility for non boolean DB */ $dsondage->mailsonde=="yes" || $dsondage->mailsonde=="true") {
        sendEmail( "$dsondage->mail_admin",
              "[".NOMAPPLICATION."] "._("Poll's participation")." : ".html_entity_decode($dsondage->titre, ENT_QUOTES, 'UTF-8')."",
              html_entity_decode("\"$nom\" ", ENT_QUOTES, 'UTF-8').
              _("has filled a line.\nYou can find your poll at the link") . " :\n\n".
              getUrlSondage($numsondage)." \n\n" .
              _("Thanks for your confidence.") . "\n". NOMAPPLICATION );
      }
    }
  } else {
    $err |= NAME_EMPTY;
  }
}
if($err != 0) {
	print_header(true, _("Error!").' - '.$dsondage->titre, $lang);
} else {
	print_header(true, $dsondage->titre, $lang);
}
echo '<body>'."\n";
framanav();
logo();
bandeau_tete();
bandeau_titre(_("Make your polls"));
sous_bandeau();
#print_r($_SESSION);
if($err != 0) {
  bandeau_titre(_("Error!"));

  echo '<div class="error"><ul>'."\n";
  if(is_error(NAME_EMPTY)) {
    echo '<li class="error">' . _("Enter a name !") . "</li>\n";
  }
  if(is_error(NAME_TAKEN)) {
    echo '<li class="error">' .
         _("The name you've chosen already exist in this poll!") .
         "</li>\n";
  }
  if(is_error(COMMENT_EMPTY) || is_error(COMMENT_USER_EMPTY)) {
    echo '<li class="error">' .
         _("Enter a name and a comment!") .
         "</li>\n";
  }
  if(is_error(COMMENT_INSERT_FAILED) ) {
    echo '<li class="error">' .
         _("Failed to insert the comment!") .
         "</li>\n";
  }
  echo '</ul></div>';


  if(is_error(NO_POLL_ID) || is_error(NO_POLL)) {
    echo '<div class=corpscentre>'."\n";
    print "<h2>" . _("This poll doesn't exist !") . "</h2>"."\n";
    print _("Back to the homepage of") . ' <a href="'.get_server_name().'"> '. NOMAPPLICATION . '</a>.'."\n";
    echo '</div>'."\n";
    bandeau_pied();
  
    echo '</body>'."\n";
    echo '</html>'."\n";
    die();
  }
}

echo '<div class="corps">'; //ajout pyg cohérence graphique
echo '<div class="imprimer"><p><a role="button" href="javascript:print()" class="button white medium">' . _('Print') . '</a></p>';
echo '<p><a role="button" class="button white medium" href="'.get_server_name().'exportcsv.php?numsondage=' . $numsondage . '">' . _('Export to CSV') . '</a></p></div>';
echo '<div class="presentationdate"> '."\n";

//affichage du titre du sondage
$titre=str_replace("\\","",$dsondage->titre);
echo '<h2>'.stripslashes($titre).'</h2>'."\n";

//affichage du nom de l'auteur du sondage
echo '<div class="initiator"><p><span class="mlabel">'. _("Initiator of the poll") .' :</span><span class="nom"> '.stripslashes($dsondage->nom_admin).'</span></p></div>'."\n";
echo '<div class="adress"><p><span class="mlabel">'._("Public link of the pool") .' : </span><code>'.getUrlSondage($dsondage->id_sondage).'</code></p></div>'."\n";


//affichage de la description du sondage
if ($dsondage->commentaires) {
  echo '<div class="admin_comment"><span class="mlabel">'._("Description: ") .'</span><br />'."\n";
  $commentaires = $dsondage->commentaires;
  $commentaires=nl2br(str_replace("\\","",$commentaires));
  echo '<span class="mcontent">'. $commentaires .'</span>';
  echo '</div>'."\n";
}
echo '</div>'."\n";

echo '<form name="formulaire" action="'.getUrlSondage($dsondage->id_sondage).'#bas" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";
echo '<input type="hidden" name="sondage" value="' . $numsondage . '"/>';
// Todo : add CSRF protection
echo '<div class="cadre"><div class="information">'."\n";
echo _("If you want to vote in this poll, you have to give your name, choose the values that fit best for you and validate with the plus button at the end of the line.") ."\n";
echo '</div>'."\n";

// Debut de l'affichage des resultats du sondage
echo '<table class="resultats">'."\n".'<thead>';

//On récupere les données et les sujets du sondage
$nblignes = $user_studs->RecordCount();

//on teste pour voir si une ligne doit etre modifiée
$testmodifier = false;
$ligneamodifier = -1;
for ($i=0;$i<$nblignes;$i++) {
  if (isset($_POST["modifierligne$i"]) || isset($_POST['modifierligne'.$i.'_x'])) {
    $ligneamodifier = $i;
  }
  
  //test pour voir si une ligne est a modifier
  if (isset($_POST['validermodifier'.$i]) || isset($_POST['validermodifier'.$i.'_x'])) {
    $modifier = $i;
    $testmodifier = true;
  }
}

//si le test est valide alors on affiche des checkbox pour entrer de nouvelles valeurs
if ($testmodifier) {
  $nouveauchoix = '';
  for ($i=0;$i<$nbcolonnes;$i++) {
    //recuperation des nouveaux choix de l'utilisateur
    if (isset($_POST["choix$i"]) && $_POST["choix$i"] == 1) {
      $nouveauchoix.="1";
    } else {
      $nouveauchoix.="0";
    }
  }
  
  $compteur=0;
  while ($data = $user_studs->FetchNextObject(false) ) {
    //mise a jour des données de l'utilisateur dans la base SQL
    if ($compteur == $modifier) {
      $sql = 'UPDATE user_studs SET reponses='.$connect->Param('nouveauchoix').' WHERE nom='.$connect->Param('nom').' AND id_users='.$connect->Param('id_users');
      $sql = $connect->Prepare($sql);
      $connect->Execute($sql, array($nouveauchoix, $data->nom, $data->id_users));
      
      if ($dsondage->mailsonde=="yes") {
        sendEmail( "$dsondage->mail_admin", "[".NOMAPPLICATION."] " . _("Poll's participation") . " : ".html_entity_decode($dsondage->titre, ENT_QUOTES, 'UTF-8'), "\"".html_entity_decode($data->nom, ENT_QUOTES, 'UTF-8')."\""."" . _("has filled a line.\nYou can find your poll at the link") . " :\n\n".getUrlSondage($numsondage)." \n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION );
      }
    }
    
    $compteur++;
  }
}

//recuperation des utilisateurs du sondage
$sql = 'SELECT * FROM user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($numsondage));

//reformatage des données des sujets du sondage
$toutsujet = explode(",",$dsondage->sujet);
//patch pyg pour réordonner les dates ajoutees a posteriori
//sort($toutsujet, SORT_NUMERIC);

//si le sondage est un sondage de date
if ($dsondage->format=="D"||$dsondage->format=="D+") {
  //affichage des sujets du sondage
  echo '<tr>'."\n";
  echo '<td></td>'."\n";
  
  //affichage des années
  $colspan=1;
  for ($i=0;$i<count($toutsujet);$i++) {
    if (isset($toutsujet[$i+1]) && date('Y', intval($toutsujet[$i])) == date('Y', intval($toutsujet[$i+1]))) {
      $colspan++;
    } else {
      echo '<td colspan='.$colspan.' class="annee">'.date('Y', intval($toutsujet[$i])).'</td>'."\n";
      $colspan=1;
    }
  }
  
  echo '</tr>'."\n";
  echo '<tr>'."\n";
  echo '<td></td>'."\n";
  
  //affichage des mois
  $colspan=1;
  for ($i=0;$i<count($toutsujet);$i++) {
    // intval() est utiliser pour supprimer le suffixe @* qui déplaît logiquement à strftime()
    $cur = intval($toutsujet[$i]);
    if (isset($toutsujet[$i+1]) === false) {
      $next = false;
    } else {
      $next = intval($toutsujet[$i+1]);
    }
    
    if ($next && strftime("%B", $cur) == strftime("%B", $next) && date('Y', $cur) == date('Y', $next)) {
      $colspan++;
    } else {
      if ($_SESSION["langue"]=="EN") { // because strftime doesn't support english suffix (like st,nd,rd,th)
        echo '<td colspan='.$colspan.' class="mois">'.date("F",$cur).'</td>'."\n";
      } else {
        echo '<td colspan='.$colspan.' class="mois">'.strftime("%B",$cur).'</td>'."\n";
      }
      $colspan=1;
    }
  }
  
  echo '</tr>'."\n";
  echo '<tr>'."\n";
  echo '<td></td>'."\n";
  
  //affichage des jours
  $colspan=1;
  for ($i=0;$i<count($toutsujet);$i++) {
    $cur = intval($toutsujet[$i]);
    if (isset($toutsujet[$i+1]) === false) {
      $next = false;
    } else {
      $next = intval($toutsujet[$i+1]);
    }
    if ($next && strftime("%a %e", $cur) == strftime("%a %e", $next) && strftime("%B", $cur) == strftime("%B", $next)) {
      $colspan++;
    } else {
      if ($_SESSION["langue"]=="EN") {
        echo '<td colspan='.$colspan.' class="jour">'.date("D jS",$cur).'</td>'."\n";
      } else {
        echo '<td colspan='.$colspan.' class="jour">'.strftime("%a %e",$cur).'</td>'."\n";
      }
      
      $colspan=1;
    }
  }
  
  echo '</tr>'."\n";
  
  //affichage des horaires
  if (strpos($dsondage->sujet, '@') !== false) {
    echo '<tr>'."\n";
     echo '<th role="presentation"></th>'."\n";
    
    for ($i=0; isset($toutsujet[$i]); $i++) {
      $heures=explode("@",$toutsujet[$i]);
      if (isset($heures[1]) === true) {
        echo '<td class="heure">'.stripslashes($heures[1]).'</td>'."\n";
      } else {
	echo '<td scope="col" class="heure"></td>'."\n";
      }
    }
    
    echo '</tr>'."\n";
  }
} else {
  $toutsujet=str_replace("°","'",$toutsujet);
  
  //affichage des sujets du sondage
  echo '<tr>'."\n";
  echo '<th role="presentation"></th>'."\n";
  
  for ($i=0; isset($toutsujet[$i]); $i++) {
    echo '<th scope="col" class="sujet">'.stripslashes($toutsujet[$i]).'</th>'."\n";
  }
  echo '<th></th>'."\n";
  echo '</tr>'."\n";
}

echo '</thead>'."\n".'<tbody>'."\n";

//Usager pré-authentifié dans la liste?
$user_mod = false;

//affichage des resultats actuels
$somme = array();
$compteur = 0;

while ($data = $user_studs->FetchNextObject(false)) {
  echo '<tr>'."\n";
  echo '<td class="nom">';
  
  // Le nom de l'utilisateur
  $nombase=str_replace("°","'",$data->nom);
  echo stripslashes($nombase).'</td>'."\n";
  
  // Les réponses qu'il a choisies
  $ensemblereponses = $data->reponses;
  
  // ligne d'un usager pré-authentifié
  $mod_ok = !( USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']) ) || ($nombase == $_SESSION['nom']);
  $user_mod |= $mod_ok;
  
  // pour chaque colonne
  for ($k=0; $k < $nbcolonnes; $k++) {
    // on remplace les choix de l'utilisateur par une ligne de checkbox pour recuperer de nouvelles valeurs
    if ($compteur == $ligneamodifier) {
      echo '<td class="vide"><input type="checkbox" title="' . _('Select the choice ') .$k.'" name="choix'.$k.'" value="1" ';
      if(substr($ensemblereponses,$k,1) == '1') {
        echo 'checked="checked"';
      }
      
      echo ' /></td>'."\n";
    } else {
      $car = substr($ensemblereponses, $k, 1);
      if ($car == "1") {
        echo '<td class="ok">OK</td>'."\n";
        if (isset($somme[$k]) === false) {
	  $somme[$k] = 0;
	}
        $somme[$k]++;
      } else {
        echo '<td class="non"></td>'."\n";
      }
    }
  }
  
  //a la fin de chaque ligne se trouve les boutons modifier
  if ($compteur != $ligneamodifier && ($dsondage->format=="A+"||$dsondage->format=="D+") && $mod_ok) {
    echo '<td class=casevide><input type="image" alt="' . _('Edit') . '" name="modifierligne'.$compteur.'" src="'.get_server_name().'images/info.png"></td>'."\n";
  }
  
  //demande de confirmation pour modification de ligne
  for ($i=0;$i<$nblignes;$i++) {
    if (isset($_POST["modifierligne$i"]) || isset($_POST['modifierligne'.$i.'_x'])) {
      if ($compteur == $i) {
        echo '<td class="casevide"><input type="image" alt="'. _('Validate my choices') .'" name="validermodifier'.$compteur.'" src="'.get_server_name().'images/accept.png" ></td>'."\n";
      }
    }
  }
  
  $compteur++;
  echo '</tr>'."\n";
}

// affichage de la ligne pour un nouvel utilisateur
if (( !(USE_REMOTE_USER && isset($_SERVER['REMOTE_USER'])) || !$user_mod) && $ligneamodifier==-1) {
  echo '<tr class="ajout_reponse">'."\n";
  echo '<td class="nom">'."\n";
  if (isset($_SESSION['nom'])) {
    $nom = stripslashes($_SESSION['nom']);
  } else {
    $nom = 'Votre nom';
  }
  echo '<input title="'. _('Your name') .'" type="text" id="'.$nom.'" name="nom" maxlength="64" value="'.$nom.'" onfocus="if (this.value == \''. _('Your name') .'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''. _('Your name') .'\';}" >'."\n";
  
  echo '</td>'."\n";
  
  // affichage des cases de formulaire checkbox pour un nouveau choix
  for ($i=0;$i<$nbcolonnes;$i++) {
    echo '<td class="vide"><input type="checkbox" title="' . _('Select the choice ').$i.'" name="choix'.$i.'" value="1"';
    if ( isset($_POST['choix'.$i]) && $_POST['choix'.$i] == '1' && is_error(NAME_EMPTY) ) {
      echo ' checked="checked"';
    }
    
    echo '></td>'."\n";
  }
  
  // Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
  echo '<td><input type="image" alt="'. _('Validate my choices') .'" name="boutonp" src="'.get_server_name().'images/add-24.png"></td>'."\n";
  echo '</tr>'."\n";
  
  // Focus javascript sur la case de texte du formulaire
  echo '<script type="text/javascript">'."\n" . 'document.formulaire.nom.focus();'."\n" . '</script>'."\n";
}

//determination de la meilleure date
// On cherche la meilleure colonne
for ($i=0; $i < $nbcolonnes; $i++) {
  if (isset($somme[$i]) === true) {
    if ($i == "0") {
      $meilleurecolonne = $somme[$i];
    }
    
    if (isset($meilleurecolonne) === false || $somme[$i] > $meilleurecolonne) {
      $meilleurecolonne = $somme[$i];
    }
  }
}

echo '</tbody>'."\n".'<tfoot>'."\n";

// Affichage des différentes sommes des colonnes existantes
echo '<tr>'."\n";
echo '<th scope="row" class="txt-right">';
// si on a plus de 8 colonnes, on affiche un second bouton "valider mes choix"
echo ($nbcolonnes>8) ?'<input type="submit" name="boutonp" alt="'. _('Validate my choices') .'" class="btn btn-success btn-mini" style="margin-right:50px">' : "";
echo   _("Addition") .'</th>'."\n";

for ($i=0; $i < $nbcolonnes; $i++) {
  if (isset($somme[$i]) === true) {
    $affichesomme = $somme[$i];
    
    if ($affichesomme == "") {
      $affichesomme = '0';
    }
  } else {
    $affichesomme = '0';
  }
  
  echo '<td class="somme">'.$affichesomme.'</td>'."\n";
}
echo '<td class="somme"></td>'."\n";
echo '</tr>'."\n";
echo '<tr>'."\n";
echo '<td class="somme"></td>'."\n";

for ($i=0; $i < $nbcolonnes; $i++) {
  if (isset($somme[$i]) && isset($meilleurecolonne) && $somme[$i] == $meilleurecolonne) {
    echo '<td class="somme"><img src="'.get_server_name().'images/medaille.png" alt="' . _('Best choice') . '"></td>'."\n";
  } else {
    echo '<td class="somme"></td>'."\n";
  }
}
echo '<td class="somme"></td>'."\n";
echo '</tr>'."\n";
echo '</tfoot>'."\n";
echo '</table>'."\n";
echo '</div>'."\n";

// reformatage des données de la base pour les sujets
$toutsujet=explode(",",$dsondage->sujet);
$toutsujet=str_replace("°","'",$toutsujet);

// On compare le nombre de résultat avec le meilleur et si le résultat est égal
//  on concatene le resultat dans $meilleursujet
$compteursujet=0;
$meilleursujet = '';

for ($i = 0; $i < $nbcolonnes; $i++) {
  if (isset($somme[$i]) && isset($meilleurecolonne) && $somme[$i] == $meilleurecolonne) {
    $meilleursujet.=", ";
    if ($dsondage->format=="D"||$dsondage->format=="D+") {
      $meilleursujetexport = $toutsujet[$i];
      if (strpos($toutsujet[$i],'@') !== false) {
        $toutsujetdate=explode("@",$toutsujet[$i]);
        if ($_SESSION["langue"]=="EN") {
          $meilleursujet.=date("l, F jS Y",$toutsujetdate[0])." " . _("for") ." ".$toutsujetdate[1];
        } else {
          $meilleursujet.=strftime(_("%A, den %e. %B %Y"),$toutsujetdate[0]). ' ' . _("for")  . ' ' . $toutsujetdate[1];
        }
      } else {
        if ($_SESSION["langue"]=="EN") {
          $meilleursujet.=date("l, F jS Y",$toutsujet[$i]);
        } else {
          $meilleursujet.=strftime(_("%A, den %e. %B %Y"),$toutsujet[$i]);
        }
      }
    } else {
      $meilleursujet .= $toutsujet[$i];
    }
    
    $compteursujet++;
  }
}

$meilleursujet=substr("$meilleursujet", 1);
$vote_str = _('vote');

if (isset($meilleurecolonne) && $meilleurecolonne > 1) {
  $vote_str = _('votes');
}

echo '<p class="affichageresultats">'."\n";

// Affichage du meilleur choix
if ($compteursujet == "1" && isset($meilleurecolonne)) {
  print '<img src="'.get_server_name().'images/medaille.png" alt=""> ' . _('The best choice at this time is:') . "<b>".stripslashes($meilleursujet)."</b> " . _('with') . " <b>$meilleurecolonne </b>" . $vote_str . ".\n";
} elseif (isset($meilleurecolonne)) {
  print '<img src="'.get_server_name().'images/medaille.png" alt=""> ' . _('The bests choices at this time are:') . " <b>".stripslashes($meilleursujet)."</b> " . _('with') . "  <b>$meilleurecolonne </b>" . $vote_str . ".\n";
}

echo '</p>';

//affichage des commentaires des utilisateurs existants
$sql = 'select * from comments where id_sondage='.$connect->Param('numsondage').' order by id_comment';
$sql = $connect->Prepare($sql);
$comment_user=$connect->Execute($sql, array($numsondage));

if ($comment_user->RecordCount() != 0) {
  print "<br /><b>" . _("Comments of polled people") . " :</b><br />\n";
  while($dcomment = $comment_user->FetchNextObject(false)) {
    print '<div class="comment"><span class="usercomment">'.stripslashes($dcomment->usercomment). ' :</span> <span class="comment">' . stripslashes(nl2br($dcomment->comment)) . '</span></div>';
  }
}

//affichage de la case permettant de rajouter un commentaire par les utilisateurs
print '<div class="addcomment">' .'<fieldset><legend>' ._("Add a comment in the poll:") . '</legend>' . "\n";

if (isset($_SESSION['nom']) === false) {
  $nom = '';
} else {
  $nom = stripslashes($_SESSION['nom']);
}
echo '<p><label for="commentator">'. _("Name") .'</label> : ';
echo '<input type="text" name="commentuser" maxlength="64" id="commentator" value="'.$nom.'" /></p>'."\n";
echo '<p><label for="comment">'. _("Your comment: ") .'</label><br />';
echo '<textarea id="comment" title="'. _("Write your comment") .'" name="comment" rows="2" cols="40"></textarea></p>'."\n";
echo '<p class="txt-center"><input type="submit" name="ajoutcomment" value="'. _("Send your comment") .'" class="button green"></p>'."\n";
echo '</fieldset></div></form>'."\n";
echo '</div>'."\n";
/* Export CSV / ICS
 *  echo '<ul class="exports">';
echo '<li><img alt="' . _('Export to CSV') . '" src="'.get_server_name().'images/csv.png"/>'.'<a class="affichageexport" href="'.get_server_name().'exportcsv.php?numsondage=' . $numsondage . '">'._("Export: Spreadsheet") .' (.CSV)' . '</a></li>';

if ( ($dsondage->format == 'D' || $dsondage->format == 'D+') && $compteursujet=="1" &&  $meilleurecolonne && file_exists('iCalcreator/iCalcreator.class.php') && false) {
  echo '<li><img alt="' . _('Export iCal') . '" src="'.get_server_name().'images/ical.png">' .'<a class="affichageexport" href="'.get_server_name().'exportics.php?numsondage=' . $numsondage . '">'._("Agenda") .' (.ICS)' . '</a></li>';
}

echo '</ul>';
*/
echo '<a id="bas"></a>'."\n";

//echo '</div>'; // ajout pyg cohérence graphique

bandeau_pied_mobile();
// Affichage du bandeau de pied
echo '</body>'."\n";
echo '</html>'."\n";
