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
