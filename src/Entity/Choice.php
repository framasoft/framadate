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
namespace Framadate\Entity;

class Choice
{

    const CHOICE_REGEX = '/^[ 012]$/';
    const MD5_REGEX = '/^[A-Fa-f0-9]{32}$/';

    /**
     * Name of the Choice
     */
    private $name;

    /**
     * All available slots for this Choice.
     */
    private $slots;

    /**
     * Choice constructor.
     * @param string $name
     */
    public function __construct($name = '')
    {
        $this->name = $name;
        $this->slots = [];
    }

    public function addSlot($slot)
    {
        $this->slots[] = $slot;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSlots()
    {
        return $this->slots;
    }

    static function compare(Choice $a, Choice $b)
    {
        return strcmp($a->name, $b->name);
    }
}
