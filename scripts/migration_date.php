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

//ouverture de la connection avec la base SQL
$connect = pg_connect("host= dbname= user=");

$sondage=pg_exec($connect, "select * from sondage where format='D' or format='D+'");

for ($i=0;$i<pg_numrows($sondage);$i++) {
  $dsondage=pg_fetch_object($sondage,$i);
  
  //  print "Pour le sondage ".$dsondage->id_sondage." ";
  $sujets=pg_exec($connect, "select sujet from sujet_studs where id_sondage='$dsondage->id_sondage'");
  $dsujets=pg_fetch_object($sujets,0);
  
  $nouvelledateaffiche="";
  $anciensujethoraires=explode(",",$dsujets->sujet);
  
  for ($j=0;$j<count($anciensujethoraires);$j++) {
    if (strpos('@',$anciensujethoraires[$j]) !== false) {
      $ancientsujet=explode("@",$anciensujethoraires[$j]);
      //;([0-2]\d)/([0-2]\d)/(\d{4});
      if (preg_match(";(\d{1,2})/(\d{1,2})/(\d{4});",$ancientsujet[0],$registredate)) {
        $nouvelledate=mktime(0,0,0,$registredate[2],$registredate[1],$registredate[3]);
        //        echo $ancientsujet[0].'@'.$ancientsujet[1].' ---> '.$nouvelledate.'@'.$ancientsujet[1].'<br> ';
        $nouvelledateaffiche.=$nouvelledate.'@'.$ancientsujet[1].',';
      }
    } else {
      if (preg_match(";(\d{1,2})/(\d{1,2})/(\d{4});",$anciensujethoraires[$j],$registredate)) {
        $nouvelledate=mktime(0,0,0,$registredate[2],$registredate[1],$registredate[3]);
        //          echo $anciensujethoraires[$j].' ---- > '.$nouvelledate.'<br>';
        $nouvelledateaffiche.=$nouvelledate.',';
      }
    }
  }
  
  $nouvelledateaffiche=substr($nouvelledateaffiche,0,-1);
  print $dsujets->sujet.' donne  '.$nouvelledateaffiche.'\n\n';
  //    pg_exec($connect,"update sujet_studs set sujet='$nouvelledateaffiche' where id_sondage='$dsondage->id_sondage'");
}