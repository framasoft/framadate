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
// TODO Move this file into a class into app/classes/Framadate
namespace Framadate;

include_once __DIR__ . '/app/inc/init.php';


/**
 * Generer une chaine de caractere unique et aleatoire
 */
function random($car)
{
// TODO Better random ?
    $string = '';
    $chaine = 'abcdefghijklmnopqrstuvwxyz123456789';
    srand((double)microtime()*1000000);
    for($i=0; $i<$car; $i++) {
      $string .= $chaine[rand()%strlen($chaine)];
    }

    return $string;
}

function ajouter_sondage($title, $comment, $adminName, $adminMail, $format, $editable, $endDate, $receiveNewVotes, $choices)
{
    global $connect;
    global $config; 
    
    // Generate poll ids
    $poll_id = random(16);
    $admin_poll_id = $poll_id.random(8);
    
    // Insert poll + slots
    $connect->beginTransaction();

    $sql = 'INSERT INTO sondage
          (poll_id, admin_poll_id, title, comment, admin_name, admin_mail, end_date, format, editable, receiveNewVotes)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?)';
    $prepared = $connect->prepare($sql);
    $prepared->execute(array($poll_id, $admin_poll_id, $title, $comment, $adminName, $adminMail, $endDate, $format, $editable, $receiveNewVotes));

    $prepared = $connect->prepare('INSERT INTO sujet_studs (id_sondage, sujet) VALUES (?, ?)');
    foreach ($choices as $choice) {
        foreach ($choice->getSlots() as $slot) {
            $prepared->execute(array($poll_id, $choice->getName().'@'.$slot));
        }
    }

    $connect->commit();

    // Send confirmation by mail if enabled
    if($config['use_smtp'] === true){
        $message = _("This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
        $message .= "\n\n";
        $message .= stripslashes(html_entity_decode($adminName, ENT_QUOTES, "UTF-8"))." " . _("hast just created a poll called") . " : \"".stripslashes(htmlspecialchars_decode($title,ENT_QUOTES))."\".\n";
        $message .= _("Thanks for filling the poll at the link above") . " :\n\n%s\n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION;

        $message_admin = _("This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
        $message_admin .= " :\n\n"."%s \n\n" . _("Thanks for your confidence.") . "\n".NOMAPPLICATION;

        $message = sprintf($message, Utils::getUrlSondage($poll_id));
        $message_admin = sprintf($message_admin, Utils::getUrlSondage($admin_poll_id, true));

        if (Utils::isValidEmail($_SESSION['adresse'])) {
            Utils::sendEmail( $adminMail, "[".NOMAPPLICATION."][" . _("Author's message")  . "] " . _("Poll") . " : ".stripslashes(htmlspecialchars_decode($title,ENT_QUOTES)), $message_admin, $_SESSION['adresse'] );
            Utils::sendEmail( $adminMail, "[".NOMAPPLICATION."][" . _("For sending to the polled users") . "] " . _("Poll") . " : ".stripslashes(htmlspecialchars_decode($title,ENT_QUOTES)), $message, $_SESSION['adresse'] );
        }
    }
    
    error_log(date('H:i:s d/m/Y:') . ' CREATION: '.$poll_id."\t".$format."\t".$adminName."\t".$adminMail."\n", 3, 'admin/logs_studs.txt');

    return $admin_poll_id;
}
