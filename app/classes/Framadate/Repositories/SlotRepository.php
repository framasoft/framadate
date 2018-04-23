<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha�l DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est r�gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Repositories;

use Framadate\Choice;
use Framadate\Utils;

class SlotRepository extends AbstractRepository {
    /**
     * Insert a bulk of slots.
     *
     * @param int $poll_id
     * @param array $choices
     */
    public function insertSlots($poll_id, $choices) {
        foreach ($choices as $choice) {
            /** @var Choice $choice */
            // We prepared the slots (joined by comas)
            $joinedSlots = null;
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
            $this->connect->insert(Utils::table('slot'), [
                'poll_id' => $poll_id,
                'title' => $choice->getName(),
                'moments' => $joinedSlots
            ]);
        }
    }

    /**
     * @param $poll_id
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function listByPollId($poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('slot') . ' WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Find the slot into poll for a given datetime.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot
     * @throws \Doctrine\DBAL\DBALException
     * @return mixed Object The slot found, or null
     */
    function findByPollIdAndDatetime($poll_id, $datetime) {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('slot') . ' WHERE poll_id = ? AND SUBSTRING_INDEX(title, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $slot = $prepared->fetch();
        $prepared->closeCursor();

        return $slot;
    }

    /**
     * Insert a new slot into a given poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $title mixed The title of the slot
     * @param $moments mixed|null The moments joined with ","
     * @return bool true if action succeeded
     */
    function insert($poll_id, $title, $moments)
    {
        return $this->connect->insert(Utils::table('slot'), ['poll_id' => $poll_id, 'title' => $title, 'moments' => $moments]) > 0;
    }

    /**
     * Update a slot into a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot to update
     * @param $newMoments mixed The new moments
     * @return bool|null true if action succeeded.
     */
    function update($poll_id, $datetime, $newMoments)
    {
        return $this->connect->update(Utils::table('slot'), ['moments' => $newMoments], ['poll_id' => $poll_id, 'title' => $datetime]) > 0;
    }

    /**
     * Delete a entire slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime mixed The datetime of the slot
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function deleteByDateTime($poll_id, $datetime)
    {
        return $this->connect->delete(Utils::table('slot'), ['poll_id' => $poll_id, 'title' => $datetime]) > 0;
    }

    /**
     * @param $poll_id
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function deleteByPollId($poll_id)
    {
        return $this->connect->delete(Utils::table('slot'), ['poll_id' => $poll_id]) > 0;
    }
}
