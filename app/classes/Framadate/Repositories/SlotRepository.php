<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphael DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphael DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class SlotRepository extends AbstractRepository {
    /**
     * Insert a bulk of slots.
     *
     * @param string $poll_id
     * @param array $choices
     */
    public function insertSlots(string $poll_id, array $choices): void
    {
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
                $prepared->execute([$poll_id, $choice->getName(), null]);
            } else {
                $prepared->execute([$poll_id, $choice->getName(), $joinedSlots]);
            }
        }
    }

    /**
     * @return array|false
     */
    public function listByPollId(string $poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Find the slot into poll for a given datetime.
     *
     * @param string $poll_id The ID of the poll
     * @param $datetime int The datetime of the slot
     * @return mixed Object The slot found, or null
     */
    public function findByPollIdAndDatetime(string $poll_id, $datetime) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND SUBSTRING_INDEX(title, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $slot = $prepared->fetch();
        $prepared->closeCursor();

        return $slot;
    }

    /**
     * Insert a new slot into a given poll.
     *
     * @param string $poll_id The ID of the poll
     * @param $title mixed The title of the slot
     * @param $moments mixed|null The moments joined with ","
     * @return bool true if action succeeded
     */
    public function insert(string $poll_id, string $title, ?string $moments): bool
    {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('slot') . '` (poll_id, title, moments) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $title, $moments]);
    }

    /**
     * Update a slot into a poll.
     *
     * @param string $poll_id The ID of the poll
     * @param $datetime int The datetime of the slot to update
     * @param $newMoments mixed The new moments
     * @return bool|null true if action succeeded.
     */
    public function update(string $poll_id, $datetime, $newMoments): ?bool
    {
        $prepared = $this->prepare('UPDATE `' . Utils::table('slot') . '` SET moments = ? WHERE poll_id = ? AND title = ?');

        return $prepared->execute([$newMoments, $poll_id, $datetime]);
    }

    /**
     * Delete a entire slot from a poll.
     *
     * @param string $poll_id int The ID of the poll
     * @param $datetime mixed The datetime of the slot
     */
    public function deleteByDateTime(string $poll_id, $datetime): void
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND title = ?');
        $prepared->execute([$poll_id, $datetime]);
    }

    public function deleteByPollId(string $poll_id): bool
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }
}
