<?php
namespace Framadate\Services;

use Framadate\Utils;

/**
 * Class AdminPollService
 *
 * @package Framadate\Services
 */
class AdminPollService {

    private $connect;
    private $pollService;

    function __construct($connect, $pollService) {
        $this->connect = $connect;
        $this->pollService = $pollService;
    }

    function updatePoll($poll) {
        return $this->connect->updatePoll($poll);
    }

    /**
     * Delete a comment from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $comment_id int The ID of the comment
     * @return mixed true is action succeeded
     */
    function deleteComment($poll_id, $comment_id) {
        return $this->connect->deleteComment($poll_id, $comment_id);
    }

    /**
     * Remove all comments of a poll.
     *
     * @param $poll_id int The ID a the poll
     * @return bool|null true is action succeeded
     */
    function cleanComments($poll_id) {
        return $this->connect->deleteCommentsByPollId($poll_id);
    }

    /**
     * Delete a vote from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $vote_id int The ID of the vote
     * @return mixed true is action succeeded
     */
    function deleteVote($poll_id, $vote_id) {
        return $this->connect->deleteVote($poll_id, $vote_id);
    }

    /**
     * Remove all votes of a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool|null true is action succeeded
     */
    function cleanVotes($poll_id) {
        return $this->connect->deleteVotesByPollId($poll_id);
    }

    /**
     * Delete the entire given poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    function deleteEntirePoll($poll_id) {
        /*$this->connect->deleteVotesByPollId($poll_id);
        $this->connect->deleteCommentsByPollId($poll_id);
        $this->connect->deleteSlotsByPollId($poll_id);
        $this->connect->deleteByPollId($poll_id);*/

        return true;
    }

    /**
     * Delete a slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $slot string The name of the slot
     * @return bool true if action succeeded
     */
    public function deleteSlot($poll_id, $slot) {
        $ex = explode('@', $slot);
        $datetime = $ex[0];
        $moment = $ex[1];

        $slots = $this->pollService->allSlotsByPollId($poll_id);

        $index = 0;
        $indexToDelete = -1;
        $newMoments = [];

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            $ex = explode('@', $aSlot->sujet);
            $moments = explode(',', $ex[1]);

            foreach ($moments as $rowMoment) {
                if ($datetime == $ex[0]) {
                    if ($moment == $rowMoment) {
                        $indexToDelete = $index;
                    } else {
                        $newMoments[] = $rowMoment;
                    }
                }
                $index++;
            }
        }

        // Remove votes
        $this->connect->beginTransaction();
        $this->connect->deleteVotesByIndex($poll_id, $indexToDelete);
        if (count($newMoments) > 0) {
            $this->connect->updateSlot($poll_id, $datetime, $datetime . '@' . implode(',', $newMoments));
        } else {
            $this->connect->deleteSlot($poll_id, $datetime);
        }
        $this->connect->commit();

        return true;
    }

    /**
     * Add a new slot to the poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new slot if no one exists for the given date</li>
     *  <li>Create a new moment if a slot already exists for the given date</li>
     * </ul>
     *
     * @param $poll_id int The ID of the poll
     * @param $new_date string The date (format: d/m/Y)
     * @param $new_moment string The moment's name
     * @return bool true if added
     */
    public function addSlot($poll_id, $new_date, $new_moment) {
        $ex = explode('/', $new_date);
        $datetime = mktime(0, 0, 0, $ex[1], $ex[0], $ex[2]);

        $slots = $this->connect->allSlotsByPollId($poll_id);
        $result = $this->findInsertPosition($slots, $datetime, $new_moment);

        // Begin transaction
        $this->connect->beginTransaction();

        if ($result == null) {
            // The moment already exists
            return false;
        } elseif ($result->slot != null) {
            $slot = $result->slot;

            $joined_moments = explode('@', $slot->sujet)[1];
            $moments = explode(',', $joined_moments);

            // Check if moment already exists (maybe not necessary)
            if (in_array($new_moment, $moments)) {
                return false;
            }

            // Update found slot
            $moments[] = $new_moment;
            sort($moments);
            $this->connect->updateSlot($poll_id, $datetime, $datetime . '@' . implode(',', $moments));

        } else {
            $this->connect->insertSlot($poll_id, $datetime . '@' . $new_moment);
        }

        $this->connect->insertDefaultVote($poll_id, $result->insert);

        // Commit transaction
        $this->connect->commit();

        return true;

    }

    /**
     * This method find where to insert a datatime+moment into a list of slots.<br/>
     * Return the {insert:X}, where X is the index of the moment into the whole poll (ex: X=0 => Insert to the first column).
     * Return {slot:Y}, where Y is not null if there is a slot existing for the given datetime.
     *
     * @param $slots array All the slots of the poll
     * @param $datetime int The datetime of the new slot
     * @param $moment string The moment's name
     * @return null|\stdClass An object like this one: {insert:X, slot:Y} where Y can be null.
     */
    private function findInsertPosition($slots, $datetime, $moment) {
        $result = new \stdClass();
        $result->slot = null;
        $result->insert = -1;

        $i = 0;

        foreach ($slots as $slot) {
            $ex = explode('@', $slot->sujet);
            $rowDatetime = $ex[0];
            $moments = explode(',', $ex[1]);

            if ($datetime == $rowDatetime) {
                $result->slot = $slot;

                foreach ($moments as $rowMoment) {
                    $strcmp = strcmp($moment, $rowMoment);
                    if ($strcmp < 0) {
                        // Here we have to insert at First place or middle of the slot
                        break(2);
                    } elseif ($strcmp == 0) {
                        // Here we dont have to insert at all
                        return null;
                    }
                    $i++;
                }

                // Here we have to insert at the end of a slot
                $result->insert = $i;
                break;
            } elseif ($datetime < $rowDatetime) {
                // Here we have to insert a new slot
                break;
            } else {
                $i += count($moments);
            }
        }
        $result->insert = $i;

        return $result;
    }

}
 