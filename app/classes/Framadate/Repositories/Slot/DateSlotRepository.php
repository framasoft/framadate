<?php
namespace Framadate\Repositories\Slot;

use Framadate\Utils;

class DateSlotRepository extends AbstractSlotRepository {
	public function templateCode() {
		return "date";
	}
	
	public function listByPollId($poll_id) {
		$slots = parent::listByPollId($poll_id);
		return self::sortSlots($slots);
	}
	
    /**
     * @return mixed
     */
    static public function sortSlots($slots) {
        uasort($slots, function ($a, $b) {
            return $a->title > $b->title;
        });
        return $slots;
    }
	
    /**
     * Find the slot into poll for a given datetime.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot
     * @return mixed Object The slot found, or null
     */
    function findByPollIdAndDatetime($poll_id, $datetime) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND SUBSTRING_INDEX(title, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $slot = $prepared->fetch();
        $prepared->closeCursor();

        return $slot;
    }

    /**
     * Delete a entire slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime mixed The datetime of the slot
     */
    function deleteByDateTime($poll_id, $datetime) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND title = ?');
        $prepared->execute([$poll_id, $datetime]);
    }
}
