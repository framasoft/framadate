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

class Choice
{
    /**
     * Name of the Choice
     */
    private $name;

    /**
     * All availables slots for this Choice.
     */
    private $slots;

    public function __construct($name='')
    {
        $this->name = $name;
        $this->slots = [];
    }

    public function addSlot($slot): void
    {
        $this->slots[] = $slot;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlots(): array
    {
        return $this->slots;
    }

    public static function compare(Choice $a, Choice $b): int
    {
        return strcmp($a->name, $b->name);
    }
}
