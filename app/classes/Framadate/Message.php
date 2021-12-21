<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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

class Message {
    var $type;
    var $message;
    var $link;
    var $linkTitle;
    var $linkIcon;
    var $includeTemplate;

    public function __construct($type, $message, $link=null, $linkTitle=null, $linkIcon=null, $includeTemplate=null) {
        $this->type = $type;
        $this->message = $message;
        $this->link = $link;
        $this->linkTitle = $linkTitle;
        $this->linkIcon = $linkIcon;
        $this->includeTemplate = $includeTemplate;
    }
}
