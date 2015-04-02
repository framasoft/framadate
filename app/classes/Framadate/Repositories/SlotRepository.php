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
namespace Framadate\Repositories;

class SlotRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

    /**
     * Insert a bulk of slots.
     *
     * @param int $poll_id
     * @param array $choices
     */
    public function insertSlots($poll_id, $choices) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('slot') . '` (poll_id, title, moments) VALUES (?, ?, ?)');

        foreach ($choices as $choice) {

            // We prepared the slots (joined by comas)
            $joinedSlots = '';
            $first = true;
            foreach ($choice->getSlots() as $slot) {
                if ($first) {
                    $joinedSlots = $slot;
                    $first = false;
                } else {
                    $joinedSlots .= ',' . $slot;
                }
            }

            // We execute the insertion
            if (empty($joinedSlots)) {
                $prepared->execute(array($poll_id, $choice->getName(), null));
            } else {
                $prepared->execute(array($poll_id, $choice->getName(), $joinedSlots));
            }

        }
    }

    function listByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? ORDER BY title');
        $prepared->execute(array($poll_id));

        return $prepared->fetchAll();
    }

}
