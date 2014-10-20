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

include_once __DIR__ . '/app/inc/init.php';

if(!isset($_GET['numsondage']) || ! preg_match(";^[\w\d]{16}$;i", $_GET['numsondage'])) {
    header('Location: studs.php');
}

$sql = 'SELECT * FROM user_studs WHERE id_sondage='.$connect->Param('numsondage').' ORDER BY id_users';
$sql = $connect->Prepare($sql);
$user_studs = $connect->Execute($sql, array($_GET['numsondage']));

$dsondage = Utils::get_sondage_from_id($_GET['numsondage']);
$nbcolonnes=substr_count($dsondage->sujet,',')+1;

$toutsujet=explode(",",$dsondage->sujet);

//affichage des sujets du sondage
$input =",";
foreach ($toutsujet as $value) {
    if ($dsondage->format=="D"||$dsondage->format=="D+") {
        if (strpos($dsondage->sujet,'@') !== false) {
            $days=explode("@",$value);
            $input.= '"'.date("j/n/Y",$days[0]).'",';
        } else {
            $input.= '"'.date("j/n/Y",$values).'",';
        }
    } else {

        preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/',$value,$md_a_img);  // Markdown [![alt](src)](href)
        preg_match_all('/!\[(.*?)\]\((.*?)\)/',$value,$md_img);                 // Markdown ![alt](src)
        preg_match_all('/\[(.*?)\]\((.*?)\)/',$value,$md_a);                    // Markdown [text](href)
        if (isset($md_a_img[2][0]) && $md_a_img[2][0]!='' && isset($md_a_img[3][0]) && $md_a_img[3][0]!='') { // [![alt](src)](href)
            $subject_text = (isset($md_a_img[1][0]) && $md_a_img[1][0]!='') ? stripslashes($md_a_img[1][0]) : _("Choice") .' '.($i+1);
        } elseif (isset($md_img[2][0]) && $md_img[2][0]!='') { // ![alt](src)
            $subject_text = (isset($md_img[1][0]) && $md_img[1][0]!='') ? stripslashes($md_img[1][0]) : _("Choice") .' '.($i+1);
        } elseif (isset($md_a[2][0]) && $md_a[2][0]!='') { // [text](href)
            $subject_text = (isset($md_a[1][0]) && $md_a[1][0]!='') ? stripslashes($md_a[1][0]) : _("Choice") .' '.($i+1);
        } else { // text only
            $subject_text = stripslashes($value);
        }
        $input.= '"'.html_entity_decode($subject_text).'",';
    }
}

$input.="\r\n";

if (strpos($dsondage->sujet,'@') !== false) {
    $input.=",";
    foreach ($toutsujet as $value) {
        $heures=explode("@",$value);
        $input.= '"'.$heures[1].'",';
    }

    $input.="\r\n";
}

while ( $data=$user_studs->FetchNextObject(false)) {
    // Le nom de l'utilisateur
    $nombase=html_entity_decode(str_replace("°","'",$data->nom));
    $input.= '"'.$nombase.'",';
    //affichage des resultats
    $ensemblereponses=$data->reponses;
    for ($k=0;$k<$nbcolonnes;$k++) {
        $car=substr($ensemblereponses,$k,1);
        switch ($car) {
            case "1": $input .= '"'._('Yes').'",'; $somme[$k]++; break;
            case "2": $input .= '"'._('Ifneedbe').'",'; break;
            default: $input .= '"'._('No').'",'; break;
        }
    }

    $input.="\r\n";
}

$filesize = strlen( $input );
$filename=$_GET["numsondage"].".csv";

header( 'Content-Type: text/csv; charset=utf-8' );
header( 'Content-Length: '.$filesize );
header( 'Content-Disposition: attachment; filename="'.$filename.'"' );
header( 'Cache-Control: max-age=10' );

echo str_replace('&quot;','""',$input);

die();
