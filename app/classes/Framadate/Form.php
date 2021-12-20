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

class Form
{
    public $title;
    public $id;
    public $description;
    public $admin_name;
    public $admin_mail;
    public $format;
    public $end_date;
    public $choix_sondage;
    public $ValueMax;

    /**
     * Tells if users can modify their choices.
     * @var int
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
    public $use_ValueMax;

    /**
     * if true, there will be a limit of voters per option
     * @var boolean
     */
    public $hidden;

    /**
     * If true, the author want to customize the URL
     * @var boolean
     */
    public $use_customized_url;

    /**
     * If true, a password will be needed to access the poll
     * @var boolean
     */
    public $use_password;

    /**
     * The password needed to access the poll, hashed. Only used if $use_password is set to true
     * @var string
     */
    public $password_hash;

    /**
     * If true, the polls results will be also visible for those without password
     * @var boolean
     */
    public $results_publicly_visible;

    /**
     * List of available choices
     */
    private $choices;

    public function __construct(){
        $this->editable = Editable::EDITABLE_BY_ALL;
        $this->clearChoices();
    }

    public function clearChoices(): void
    {
        $this->choices = [];
    }

    public function addChoice(Choice $choice): void
    {
        $this->choices[] = $choice;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function sortChoices(): void
    {
        usort($this->choices, [Choice::class, 'compare']);
    }
}
