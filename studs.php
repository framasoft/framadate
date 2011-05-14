<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use, 
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info". 
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================

session_start();

if (file_exists('bandeaux_local.php'))
	include_once('bandeaux_local.php');
else
	include_once('bandeaux.php');
include_once('fonctions.php');

// Le fichier studs.php sert a afficher les résultats d'un sondage à un simple utilisateur. 
// C'est également l'interface pour ajouter une valeur à un sondage deja créé.
$numsondage = false;

//On récupère le numéro de sondage par le lien web.
if(isset($_GET["sondage"])) {
  $numsondage=$_GET["sondage"];
  $_SESSION["numsondage"] = $numsondage;
 }
if(isset($_POST["sondage"])) {
  $numsondage=$_POST["sondage"];
  $_SESSION["numsondage"] = $numsondage;
 }
elseif(isset($_COOKIE["sondage"])) {
  $numsondage=$_COOKIE["sondage"];
}
elseif(isset($_SESSION["numsondage"])) {
  $numsondage=$_SESSION["numsondage"];
}

if($numsondage) {
  $dsondage = get_sondage_from_id($numsondage);
  if(!$dsondage)
    $err |= NO_POLL;
}
else
  $err |= NO_POLL_ID;


//output a CSV and die()
if(isset($_GET['export']) && $Sondage) {
  if($_GET['export'] == 'csv')
    require_once('exportcsv.php');
  if($_GET['export'] == 'ics' && $Sondage->is_date)
    require_once('exportics.php');
  die();
 }

// quand on ajoute un commentaire utilisateur
if(isset($_POST['ajoutcomment'])) {
  if (isset($_SERVER['REMOTE_USER']))
    $comment_user = $_SESSION['nom'];
  elseif(isset($_POST["commentuser"]) && ! empty($_POST["commentuser"]))
    $comment_user = $connect->qstr(strip_tags($_POST["commentuser"]));
  elseif(isset($_POST["commentuser"]))
    $err |= COMMENT_USER_EMPTY;
  else
    $comment_user = _('anonyme');
  if(empty($_POST["comment"]))
    $err |= COMMENT_EMPTY;

  if (isset($_POST["comment"]) &&
      ! is_error(COMMENT_EMPTY) && ! is_error(NO_POLL) &&
      ! is_error(COMMENT_USER_EMPTY)) {
    if( ! $connect->Execute('INSERT INTO comments ' .
			    '(id_sondage, comment, usercomment) VALUES ("'.
			    $numsondage . '","'.
			    $connect->qstr(strip_tags($_POST['comment'])).
			    '","' .
			    $comment_user .'")') );
    $err |= COMMENT_INSERT_FAILED;
  }
}


// Action quand on clique le bouton participer
$user_studs=$connect->Execute("SELECT * FROM user_studs WHERE id_sondage='$numsondage' ORDER BY id_users");
$nbcolonnes=substr_count($dsondage->sujet,',')+1;
if ( ! is_error(NO_POLL) && isset($_POST["boutonp"]) ){
  //Si le nom est bien entré
  if (! isset($_POST["nom"]) || empty($_POST['nom']))
    $err |= NAME_EMPTY;
  if(! is_error(NAME_EMPTY) && 
     (!isset($_SERVER['REMOTE_USER']) || $_POST["nom"] == $_SESSION["nom"]) ) {
    for ($i=0;$i<$nbcolonnes;$i++){
      // Si la checkbox est enclenchée alors la valeur est 1
      if (isset($_POST["choix$i"]) && $_POST["choix$i"] == '1'){
	$nouveauchoix.="1";
      }
      // sinon c'est 0
      else {
	$nouveauchoix.="0";
      }
    }

    while($user = $user_studs->FetchNextObject(false)) {
      if ($_POST["nom"]==$user->nom)
	$err |= NAME_TAKEN;
    }

    // Ecriture des choix de l'utilisateur dans la base
    if (! is_error(NAME_TAKEN) && ! is_error(NAME_EMPTY)) {
      // TODO: throw error
      $nom=substr($_POST["nom"],0,64);
      $connect->Execute('INSERT INTO user_studs (nom,id_sondage,reponses) VALUES ("' .
			$nom . '","' .
			$numsondage . '","' .
			$nouveauchoix . '")');

      if ($dsondage->mailsonde ||
	  /* compatibility for non boolean DB */
	  $dsondage->mailsonde=="yes" || $dsondage->mailsonde=="true") {
	
	$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
	mail ("$dsondage->mail_admin",
	      "[".NOMAPPLICATION."] "._("Poll's participation")." : $dsondage->titre",

	      "\"$nom\" ".
	      _("has filled a line.\nYou can find your poll at the link") . " :\n\n".
	      get_server_name()."/studs.php?sondage=$numsondage \n\n" .
	      _("Thanks for your confidence.") . "\n". NOMAPPLICATION,
	      $headers);
      }
    }
  }
  else
    $err |= NAME_EMPTY;
}

print_header(true);
echo '<body>'."\n";
logo();
bandeau_tete();
bandeau_titre(_("Make your polls"));
sous_bandeau();

if($err != 0) {
  bandeau_titre(_("Error!"));

  echo '<div class="error"><ul>'."\n";
  if(is_error(NAME_EMPTY))
    echo '<li class="error">' . _("Enter a name !") . "</li>\n";
  if(is_error(NAME_TAKEN))
    echo '<li class="error">' .
      _("The name you've chosen already exist in this poll!") .
      "</li>\n";
  if(is_error(COMMENT_EMPTY) || is_error(COMMENT_USER_EMPTY))
    echo '<li class="error">' .
      _("Enter a name and a comment!") .
      "</li>\n";
  if(is_error(COMMENT_INSERT_FAILED) )
    echo '<li class="error">' .
      _("Failed to insert the comment!") .
      "</li>\n";

  echo '</ul></div>';

  if(is_error(NO_POLL_ID) || is_error(NO_POLL)) {
    echo '<div class=corpscentre>'."\n";
    print "<H2>" . _("This poll doesn't exist !") . "</H2>"."\n";
    print _("Back to the homepage of") . ' <a href="index.php"> '. NOMAPPLICATION .
      '</a>.'."\n";
    echo '<br><br><br><br>'."\n";
    echo '</div>'."\n";
    bandeau_pied();
  
    echo '</body>'."\n";
    echo '</html>'."\n";
    die();
  }
 }

echo '<div class="presentationdate"> '."\n";

//affichage du titre du sondage
        $titre=str_replace("\\","",$dsondage->titre);       
	echo '<H2>'.$titre.'</H2>'."\n";

//affichage du nom de l'auteur du sondage
echo _("Initiator of the poll") .' : '.$dsondage->nom_admin.'<br><br>'."\n";

//affichage des commentaires du sondage
	if ($dsondage->commentaires){
		echo _("Comments") .' :<br>'."\n";
                $commentaires=$dsondage->commentaires;
                $commentaires=str_replace("\\","",$commentaires);       
                echo $commentaires;

		echo '<br>'."\n";
	}
	echo '<br>'."\n";
echo '</div>'."\n";

echo '<form name="formulaire" action="studs.php"' .
'#bas" method="POST" onkeypress="javascript:process_keypress(event)">'."\n";

echo '<input type="hidden" name="sondage" value="' . $numsondage . '"/>';

	echo '<div class="cadre"> '."\n";

	echo _("If you want to vote in this poll, you have to give your name, choose the values that fit best for you<br>(without paying attention to the choices of the other voters) and validate with the plus button at the end of the line.") ."\n";

	echo '<br><br>'."\n";

// Debut de l'affichage des resultats du sondage
	echo '<table class="resultats">'."\n";

//On récupere les données et les sujets du sondage
	$nblignes=$user_studs->RecordCount();

	
			//on teste pour voir si une ligne doit etre modifiée
$testmodifier = false;
$ligneamodifier = -1;
		for ($i=0;$i<$nblignes;$i++){
			if (isset($_POST["modifierligne$i"]) || isset($_POST['modifierligne'.$i.'_x'])){
				$ligneamodifier=$i;
			}
			//test pour voir si une ligne est a modifier
			if ($_POST['validermodifier'.$i.'_x']){
				$modifier=$i;
				$testmodifier=true;
			}
		}
		//si le test est valide alors on affiche des checkbox pour entrer de nouvelles valeurs
		if ($testmodifier){

			for ($i=0;$i<$nbcolonnes;$i++){
				//recuperation des nouveaux choix de l'utilisateur
				if (isset($_POST["choix$i"]) && $_POST["choix$i"] == 1){
					$nouveauchoix.="1";
				}
				else {
					$nouveauchoix.="0";
				}
			}

			$compteur=0;
			while ($data=$user_studs->FetchNextObject(false) ) {
				//mise a jour des données de l'utilisateur dans la base SQL
				if ($compteur==$modifier){
					$connect->Execute("UPDATE user_studs SET reponses='$nouveauchoix' WHERE nom='$data->nom' AND id_users='$data->id_users'");
					if ($dsondage->mailsonde=="yes"){
						
						$headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
						mail ("$dsondage->mail_admin", "[".NOMAPPLICATION."] " . _("Poll's participation") . " : $dsondage->titre", "\"$data->nom\""."" . _("has filled a line.\nYou can find your poll at the link") . " :\n\n".get_server_name()."/studs.php?sondage=$numsondage \n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION,$headers);
					}
				}
				$compteur++;
			}
		}
	
//recuperation des utilisateurs du sondage
	$user_studs=$connect->Execute("SELECT * FROM user_studs WHERE id_sondage='$numsondage' ORDER BY id_users");


//reformatage des données des sujets du sondage
	$toutsujet=explode(",",$dsondage->sujet);

//si le sondage est un sondage de date
if ($dsondage->format=="D"||$dsondage->format=="D+"){
	
//affichage des sujets du sondage
	echo '<tr>'."\n";
	echo '<td></td>'."\n";

	//affichage des années
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++){
	  if (date('Y', intval($toutsujet[$i])) == date('Y', intval($toutsujet[$i+1]))){
			$colspan++;
		}
		else {
		  echo '<td colspan='.$colspan.' class="annee">'.date('Y', intval($toutsujet[$i])).'</td>'."\n";
		  $colspan=1;
		}
	}
	echo '</tr>'."\n";
	
	echo '<tr>'."\n";	
	echo '<td></td>'."\n";
	//affichage des mois
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++){
	  // intval() est utiliser pour supprimer le suffixe @* qui déplaît logiquement à strftime()
	  $cur = intval($toutsujet[$i]);
	  $next = intval($toutsujet[$i+1]);
		if (strftime("%B", $cur) == strftime("%B", $next)&&date('Y',$cur)==date('Y',$next)){
			$colspan++;
		}
		else {
			if ($_SESSION["langue"]=="EN") // because strftime doesn't support english suffix (like st,nd,rd,th)
			  echo '<td colspan='.$colspan.' class="mois">'.date("F",$cur).'</td>'."\n";
			else 
			  echo '<td colspan='.$colspan.' class="mois">'.strftime("%B",$cur).'</td>'."\n";
			$colspan=1;
		}
	}
	echo '</tr>'."\n";

	echo '<tr>'."\n";		
	echo '<td></td>'."\n";
		//affichage des jours
	$colspan=1;
	for ($i=0;$i<count($toutsujet);$i++){
	  $cur = intval($toutsujet[$i]);
	  $next = intval($toutsujet[$i+1]);
		if (strftime("%a %e",$cur)==strftime("%a %e",$next)&&strftime("%B",$cur)==strftime("%B",$next)){
			$colspan++;
		}
		else {
			if ($_SESSION["langue"]=="EN")
			  echo '<td colspan='.$colspan.' class="jour">'.date("D jS",$cur).'</td>'."\n";
			else
			  echo '<td colspan='.$colspan.' class="jour">'.strftime("%a %e",$cur).'</td>'."\n";
			$colspan=1;
		}
	}
	echo '</tr>'."\n";
		//affichage des horaires	
	if (strpos($dsondage->sujet, '@') !== false){
		echo '<tr>'."\n";
		echo '<td></td>'."\n";
				
		for ($i=0;$toutsujet[$i];$i++){
			$heures=explode("@",$toutsujet[$i]);
			echo '<td class="heure">'.$heures[1].'</td>'."\n";
		}
		echo '</tr>'."\n";
	}
}

else {
	$toutsujet=str_replace("°","'",$toutsujet);	

//affichage des sujets du sondage
	echo '<tr>'."\n";
	echo '<td></td>'."\n";

	for ($i=0;$toutsujet[$i];$i++){
	
		echo '<td class="sujet">'.$toutsujet[$i].'</td>'."\n";
	}
	echo '</tr>'."\n";

}
	
//Usager pré-authentifié dans la liste?
	$user_mod = FALSE;
//affichage des resultats actuels
$somme = array();
	$compteur = 0;

	while ($data=$user_studs->FetchNextObject(false)) {

		echo '<tr>'."\n";
		echo '<td class="nom">';

// Le nom de l'utilisateur
		$nombase=str_replace("°","'",$data->nom);
		echo $nombase.'</td>'."\n";
// Les réponses qu'il a choisit
		$ensemblereponses=$data->reponses;
// ligne d'un usager pré-authentifié
		$mod_ok = !isset($_SERVER['REMOTE_USER']) || ($nombase == $_SESSION['nom']);
		$user_mod |= $mod_ok;
		// pour chaque colonne
		for ($k=0; $k < $nbcolonnes; $k++){
		  // on remplace les choix de l'utilisateur par une ligne de checkbox pour recuperer de nouvelles valeurs
		  if ($compteur == $ligneamodifier) {
		    echo '<td class="vide"><input type="checkbox" name="choix'.$k.'" value="1" ';
		    if(substr($ensemblereponses,$k,1) == '1')
		      echo 'checked="checked"';
		    echo ' /></td>'."\n";
		  }
		  else {
		    $car=substr($ensemblereponses,$k,1);
		    if ($car=="1"){
		      echo '<td class="ok">OK</td>'."\n";
		      $somme[$k]++;
		    }
		    else {
		      echo '<td class="non"></td>'."\n";
		    }
		  }
		}
			
			//a la fin de chaque ligne se trouve les boutons modifier
		if ($compteur != $ligneamodifier && ($dsondage->format=="A+"||$dsondage->format=="D+") && $mod_ok){
		  echo '<td class=casevide><input type="image" name="modifierligne'.$compteur.'" value="Modifier" src="images/info.png"></td>'."\n";
		}
			
			//demande de confirmation pour modification de ligne
			for ($i=0;$i<$nblignes;$i++){
				if (isset($_POST["modifierligne$i"])||isset($_POST['modifierligne'.$i.'_x'])){
					if ($compteur==$i){
						echo '<td class="casevide"><input type="image" name="validermodifier'.$compteur.'" value="Valider la modification" src="images/accept.png" ></td>'."\n";
					}
				}
			}
			$compteur++;
			echo '</tr>'."\n";
	}
	
// affichage de la ligne pour un nouvel utilisateur
	if (!isset($_SERVER['REMOTE_USER']) || !$user_mod) {
		echo '<tr>'."\n";
		echo '<td class="nom">'."\n";
		if (isset($_SERVER['REMOTE_USER']))
			echo '<input type=hidden name="nom" value="'.
			      $_SESSION['nom'].'">'.$_SESSION['nom']."\n";
		else
			echo '<input type=text name="nom" maxlength="64">'."\n";
		echo '</td>'."\n";

// affichage des cases de formulaire checkbox pour un nouveau choix
		for ($i=0;$i<$nbcolonnes;$i++){
		  echo '<td class="vide"><input type="checkbox" name="choix'.$i.'" value="1"';
		  if ( isset($_POST['choix'.$i]) && $_POST['choix'.$i] == '1' && is_error(NAME_EMPTY) )
		    echo ' checked="checked"';
		  echo '></td>'."\n";
		}
		// Affichage du bouton de formulaire pour inscrire un nouvel utilisateur dans la base
		echo '<td><input type="image" name="boutonp" value="' . _('Participate') . '" src="images/add-24.png"></td>'."\n";
		echo '</tr>'."\n";
	}

//determination de la meilleure date

// On cherche la meilleure colonne
	for ($i=0;$i<$nbcolonnes;$i++){
		if ($i=="0"){
			$meilleurecolonne=$somme[$i];
		}
		if ($somme[$i]>$meilleurecolonne){
			$meilleurecolonne=$somme[$i];
		}
	}

// Affichage des différentes sommes des colonnes existantes
	echo '<tr>'."\n";
	echo '<td align="right">'. _("Addition") .'</td>'."\n";

	for ($i=0;$i<$nbcolonnes;$i++){
		$affichesomme=$somme[$i];
		if ($affichesomme==""){$affichesomme="0";}
		echo '<td class="somme">'.$affichesomme.'</td>'."\n";
	}
	echo '</tr>'."\n";
	
	echo '<tr>'."\n";
	echo '<td class="somme"></td>'."\n";
	for ($i=0;$i<$nbcolonnes;$i++){
		if ($somme[$i]==$meilleurecolonne&&$somme[$i]){
		  echo '<td class="somme"><img src="images/medaille.png" alt="' . _('Best choice') . '"></td>'."\n";
		}
		else {
			echo '<td class="somme"></td>'."\n";
		}

		}
	echo '</tr>'."\n";

	echo '</table>'."\n";
	echo '</div>'."\n";

// reformatage des données de la base pour les sujets
	$toutsujet=explode(",",$dsondage->sujet);
	$toutsujet=str_replace("°","'",$toutsujet);

// On compare le nombre de résultat avec le meilleur et si le résultat est égal
//  on concatene le resultat dans $meilleursujet

	$compteursujet=0;
	for ($i=0;$i<$nbcolonnes;$i++){
		if ($somme[$i]==$meilleurecolonne){
			$meilleursujet.=", ";
			  	if ($dsondage->format=="D"||$dsondage->format=="D+"){
					$meilleursujetexport=$toutsujet[$i];
					if (strpos($toutsujet[$i],'@') !== false){
						$toutsujetdate=explode("@",$toutsujet[$i]);
						if ($_SESSION["langue"]=="EN")
						  $meilleursujet.=date("l, F jS Y",$toutsujetdate[0])." " . _("for") ." ".$toutsujetdate[1];
						else
						  $meilleursujet.=strftime(_("%A, den %e. %B %Y"),$toutsujetdate[0]). ' ' . _("for")  . ' ' . $toutsujetdate[1];
					}
					else {
					  if ($_SESSION["langue"]=="EN")
					    $meilleursujet.=date("l, F jS Y",$toutsujet[$i]);
					  else
					    $meilleursujet.=strftime(_("%A, den %e. %B %Y"),$toutsujet[$i]);
					}
				}
				else
				  $meilleursujet.=$toutsujet[$i];
				
			$compteursujet++;
		}
	}
	$meilleursujet=substr("$meilleursujet",1);

$vote_str = _('vote');
if ($meilleurecolonne > 1)
  $vote_str = _('votes');

	echo '<p class="affichageresultats">'."\n";

	// Affichage du meilleur choix
	if ($compteursujet=="1"&&$meilleurecolonne){
	  print '<img src="images/medaille.png" alt="Meilleur choix"> ' . _('The best choice at this time is:') . "<b>$meilleursujet</b> " . _('with') . " <b>$meilleurecolonne </b>" . $vote_str . ".\n";
 	}
	elseif ($meilleurecolonne){
	  print '<img src="images/medaille.png" alt="Meilleur choix"> ' . _('The bests choices at this time are:') . " <b>$meilleursujet</b> " . _('with') . "  <b>$meilleurecolonne </b>" . $vote_str . ".\n";
	}
	
	echo '</p>';
	
	//affichage des commentaires des utilisateurs existants
	$comment_user=$connect->Execute("select * from comments where id_sondage='$numsondage' order by id_comment");
	if ($comment_user->RecordCount() != 0) {

		print "<br><b>" . _("Comments of polled people") . " :</b><br>\n";
		while( $dcomment=$comment_user->FetchNextObject(false)) {
		  print $dcomment->usercomment . ' : ' . $dcomment->comment . '<br />';
		}

	}
	
	//affichage de la case permettant de rajouter un commentaire par les utilisateurs
	print '<div class="addcomment">' .
	'<p>' ._("Add a comment in the poll:") . '</p>' . "\n";

if (isset($_SERVER['REMOTE_USER']) || isset($_SESSION['nom'])) {
  echo _("Name") .' : ';
  echo '<input type="text" name="commentuser" maxlength="64" /><br>'."\n";
}
echo '<textarea name="comment" rows="2" cols="40"></textarea>'."\n";
echo '<input type="image" name="ajoutcomment" value="Ajouter un commentaire" src="images/accept.png" alt="Valider"><br>'."\n";
echo '</form>'."\n";
// Focus javascript sur la case de texte du formulaire
echo '<script type="text/javascript">'."\n" . 'document.formulaire.commentuser.focus();'."\n" . '</script>'."\n";

echo '</div>'."\n";

echo '<ul class="exports">';
echo '<li><img alt="' . _('Export to CSV') . '" src="images/csv.png"/>'.
'<a class="affichageexport" href="exportcsv.php?numsondage=' . $numsondage . '">'.
_("Export: Spreadsheet") .' (.CSV)' . '</a></li>';

if ( ($dsondage->format == 'D' || $dsondage->format == 'D+') &&
    $compteursujet=="1" && 
    $meilleurecolonne &&
    file_exists('iCalcreator/iCalcreator.class.php') 
     && false /* TODO: later */){
  echo '<li><img alt="' . _('Export iCal') . '" src="images/ical.png">' .
    '<a class="affichageexport" href="exportics.php?numsondage=' . $numsondage . '">'.
    _("Agenda") .' (.ICS)' . '</a></li>';
 }
echo '</ul>';
echo '<a name="bas"></a>'."\n";

	bandeau_pied_mobile();
	// Affichage du bandeau de pied
	echo '</body>'."\n";
	echo '</html>'."\n";

?>
