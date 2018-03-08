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
use MyCLabs\Enum\Enum;

/**
 * Class Editable
 *
 * Is used to specify the poll's edition permissions.
 *
 * @method static EDITABLE_BY_ALL()
 * @method static EDITABLE_BY_OWN()
 * @method static NOT_EDITABLE()
 * @package Framadate
 */
class Editable extends Enum {
    const __default = self::EDITABLE_BY_ALL;

    const NOT_EDITABLE = 0;
    const EDITABLE_BY_ALL = 1;
    const EDITABLE_BY_OWN = 2;

    public function __toString()
    {
        return (string) $this->value;
    }
}
