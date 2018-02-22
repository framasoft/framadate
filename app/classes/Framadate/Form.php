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

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Accessor;

class Form
{
    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $title;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $id;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $description;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $admin_name;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $admin_mail;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $format;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $end_date;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $choix_sondage;

    /**
     * @var string
     * @Serializer\Type("string")
     */
    public $ValueMax;

    /**
     * Tells if users can modify their choices.
     * @var \Framadate\Editable
     * @Serializer\Type("int")
     * @Accessor(getter="getEditable",setter="setEditable")
     */
    public $editable;

    /**
     * If true, notify poll administrator when new vote is made.
     * @Serializer\Type("string")
     */
    public $receiveNewVotes;

    /**
     * If true, notify poll administrator when new comment is posted.
     * @Serializer\Type("string")
     */
    public $receiveNewComments;

    /**
     * If true, only the poll maker can see the poll's results
     * @var boolean
     * @Serializer\Type("bool")
     */
    public $use_ValueMax;

    /**
     * if true, there will be a limit of voters per option
     * @var boolean
     * @Serializer\Type("bool")
     */
    public $hidden;

    /**
     * If true, the author want to customize the URL
     * @var boolean
     * @Serializer\Type("bool")
     */
    public $use_customized_url;

    /**
     * If true, a password will be needed to access the poll
     * @var boolean
     * @Serializer\Type("bool")
     */
    public $use_password;

    /**
     * The password needed to access the poll, hashed. Only used if $use_password is set to true
     * @var string
     * @Serializer\Type("string")
     */
    public $password_hash;

    /**
     * If true, the polls results will be also visible for those without password
     * @var boolean
     * @Serializer\Type("bool")
     */
    public $results_publicly_visible;

    /**
     * List of available choices
     *
     * @var array<Choice>
     * @Serializer\Type("array<Choice>")
     */
    private $choices;

    public function __construct(){
        $this->editable = Editable::EDITABLE_BY_ALL();
        $this->clearChoices();
    }

    public function clearChoices() {
        $this->choices = [];
    }

    public function addChoice(Choice $choice)
    {
        $this->choices[] = $choice;
    }

    /**
     * @return array<Choice>
     */
    public function getChoices()
    {
        return $this->choices;
    }

    public function sortChoices()
    {
        usort($this->choices, ['Framadate\Choice', 'compare']);
    }

    /**
     * @return Editable
     */
    public function getEditable()
    {
        return $this->editable->getValue();
    }

    /**
     * @param Editable $value
     */
    public function setEditable($value) {
        $this->editable = new Editable($value);
    }
}
