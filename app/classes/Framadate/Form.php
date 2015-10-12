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

class Form
{

    public $title;
    public $description;
    public $admin_name;
    public $admin_mail;
    public $format;
    public $end_date;
    public $choix_sondage;

    /**
     * Tells if users can modify their choices.
     * @var \Framadate\Editable
     */
    public $editable;

    /**
     * If true, notify poll administrator when new vote is made.
     */
    public $receiveNewVotes;

    /**
     * If true, notify poll administrator when new comment is posted.
     */
    public $receiveNewComments;

    /**
     * If true, only the poll maker can see the poll's results
     * @var boolean
     */
    public $hidden;

    /**
     * List of available choices
     */
    private $choices;

    public function __construct(){
        $this->editable = Editable::EDITABLE_BY_ALL;
        $this->clearChoices();
    }

    public function clearChoices() {
        $this->choices = array();
    }

    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function sortChoices()
    {
        usort($this->choices, array('Framadate\Choice', 'compare'));
    }

}