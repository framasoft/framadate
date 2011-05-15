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
  
  $headers="From: ".NOMAPPLICATION." <".ADRESSEMAILADMIN.">\r\nContent-Type: text/plain; charset=\"UTF-8\"\nContent-Transfer-Encoding: 8bit";
  
  global $connect;
  
  $connect->Execute('insert into sondage ' .
                    '(id_sondage, commentaires, mail_admin, nom_admin, titre, id_sondage_admin, date_fin, format, mailsonde) ' .
                    'VALUES '.
                    "('$sondage','$_SESSION[commentaires]', '$_SESSION[adresse]', '$_SESSION[nom]', '$_SESSION[titre]','$sondage_admin', FROM_UNIXTIME('$date_fin'), '$_SESSION[formatsondage]','$_SESSION[mailsonde]'  )");
  $connect->Execute("insert into sujet_studs values ('$sondage', '$_SESSION[toutchoix]' )");
  
  mail ("$_SESSION[adresse]", "[".NOMAPPLICATION."][" . _("For sending to the polled users") . "] " . _("Poll") . " : ".stripslashes($_SESSION["titre"]), "" . _("This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.") . "\n\n".stripslashes($_SESSION["nom"])." " . _("hast just created a poll called") . " : \"".stripslashes($_SESSION["titre"])."\".\n" . _("Thanks for filling the poll at the link above") . " :\n\n".get_server_name()."studs.php?sondage=$sondage \n\n" . _("Thanks for your confidence") . ",\n".NOMAPPLICATION,$headers);
  mail ("$_SESSION[adresse]", "[".NOMAPPLICATION."][" . _("Author's message") . "] " . _("Poll") . " : ".stripslashes($_SESSION["titre"]),
        _("This message should NOT be sended to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above") .
        " :\n\n".get_server_name()."adminstuds.php?sondage=$sondage_admin \n\n" . _("Thanks for your confidence") . ",\n".NOMAPPLICATION,$headers);
  
  $date=date('H:i:s d/m/Y:');
  error_log($date . " CREATION: $sondage\t$_SESSION[formatsondage]\t$_SESSION[nom]\t$_SESSION[adresse]\t \t$_SESSION[toutchoix]\n", 3, 'admin/logs_studs.txt');
  header("Location:studs.php?sondage=$sondage");
  exit();
  session_unset();
}