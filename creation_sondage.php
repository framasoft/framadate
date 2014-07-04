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

if (session_id() == "") {
  session_start();
}

include_once('fonctions.php');


//Generer une chaine de caractere unique et aleatoire
function random($car)
{
  $string = "";
  $chaine = "abcdefghijklmnopqrstuvwxyz123456789";
  srand((double)microtime()*1000000);
  for($i=0; $i<$car; $i++) {
    $string .= $chaine[rand()%strlen($chaine)];
  }
  return $string;
}

function ajouter_sondage()
{
  $sondage=random(16);
  $sondage_admin=$sondage.random(8);
  
  if ($_SESSION["formatsondage"]=="A"||$_SESSION["formatsondage"]=="A+") {
    //extraction de la date de fin choisie
    if ($_SESSION["champdatefin"]) {
      if ($_SESSION["champdatefin"]>time()+250000) {
        $date_fin=$_SESSION["champdatefin"];
      }
    } else {
      $date_fin=time()+15552000;
    }
  }
  
  if ($_SESSION["formatsondage"]=="D"||$_SESSION["formatsondage"]=="D+") {
    //Calcul de la date de fin du sondage
    $taille_tableau=sizeof($_SESSION["totalchoixjour"])-1;
    $date_fin=$_SESSION["totalchoixjour"][$taille_tableau]+200000;
  }
  
  if (is_numeric($date_fin) === false) {
    $date_fin = time()+15552000;
  }
  
  global $connect;
  
  $sql = 'INSERT INTO sondage
          (id_sondage, commentaires, mail_admin, nom_admin, titre, id_sondage_admin, date_fin, format, mailsonde)
          VALUES (
          '.$connect->Param('id_sondage').',
          '.$connect->Param('commentaires').',
          '.$connect->Param('mail_admin').',
          '.$connect->Param('nom_admin').',
          '.$connect->Param('titre').',
          '.$connect->Param('id_sondage_admin').',
          FROM_UNIXTIME('.$date_fin.'),
          '.$connect->Param('format').',
          '.$connect->Param('mailsonde').'
          )';
  $sql = $connect->Prepare($sql);
  $res = $connect->Execute($sql, array($sondage, $_SESSION['commentaires'], $_SESSION['adresse'], $_SESSION['nom'], $_SESSION['titre'], $sondage_admin, $_SESSION['formatsondage'], $_SESSION['mailsonde']));
  
  $sql = 'INSERT INTO sujet_studs values ('.$connect->Param('sondage').', '.$connect->Param('choix').')';
  $sql = $connect->Prepare($sql);
  $connect->Execute($sql, array($sondage, $_SESSION['toutchoix']));
  
  $message = _("This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
  $message .= "\n\n";
  $message .= stripslashes(html_entity_decode($_SESSION["nom"],ENT_QUOTES,"UTF-8"))." " . _("hast just created a poll called") . " : \"".stripslashes(htmlspecialchars_decode($_SESSION["titre"],ENT_QUOTES))."\".\n";
  $message .= _("Thanks for filling the poll at the link above") . " :\n\n%s\n\n" . _("Thanks for your confidence") . ",\n".NOMAPPLICATION;
  
  $message_admin = _("This message should NOT be sended to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
  $message_admin .= " :\n\n"."%s \n\n" . _("Thanks for your confidence") . ",\n".NOMAPPLICATION;
  
  $message = sprintf($message, getUrlSondage($sondage));
  $message_admin = sprintf($message_admin, getUrlSondage($sondage_admin, true));
  
  if (validateEmail($_SESSION['adresse'])) {
    sendEmail( "$_SESSION[adresse]", "[".NOMAPPLICATION."][" . _("Author's message")  . "] " . _("Poll") . " : ".stripslashes(htmlspecialchars_decode($_SESSION["titre"],ENT_QUOTES)), $message_admin, $_SESSION['adresse'] );
    sendEmail( "$_SESSION[adresse]", "[".NOMAPPLICATION."][" . _("For sending to the polled users") . "] " . _("Poll") . " : ".stripslashes(htmlspecialchars_decode($_SESSION["titre"],ENT_QUOTES)), $message, $_SESSION['adresse'] );
  }
  
  $date=date('H:i:s d/m/Y:');
  error_log($date . " CREATION: $sondage\t$_SESSION[formatsondage]\t$_SESSION[nom]\t$_SESSION[adresse]\t \t$_SESSION[toutchoix]\n", 3, 'admin/logs_studs.txt');
  header("Location:".getUrlSondage($sondage));
  exit();
  session_unset();
}
