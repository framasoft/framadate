<?php
namespace Framadate\Repositories\Slot;

use \Framadate\Repositories\AbstractRepository;
use Framadate\Utils;

abstract class AbstractSlotRepository extends AbstractRepository {
	abstract public function templateCode();
	
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
                $prepared->execute([$poll_id, $choice->getName(), null]);
            } else {
                $prepared->execute([$poll_id, $choice->getName(), $joinedSlots]);
            }
        }
    }

    function listByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Insert a new slot into a given poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $title mixed The title of the slot
     * @param $moments mixed|null The moments joined with ","
     * @return bool true if action succeeded
     */
    function insert($poll_id, $title, $moments) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('slot') . '` (poll_id, title, moments) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $title, $moments]);
    }

    /**
     * Update a slot into a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot to update
     * @param $newMoments mixed The new moments
     * @return bool|null true if action succeeded.
     */
    function update($poll_id, $datetime, $newMoments) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('slot') . '` SET moments = ? WHERE poll_id = ? AND title = ?');

        return $prepared->execute([$newMoments, $poll_id, $datetime]);
    }

    function deleteByPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }
}
