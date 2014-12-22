<?php
namespace Framadate\Services;

/**
 * Class AdminPollService
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
        return $this->connect->deleteCommentsByAdminPollId($poll_id);
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
        return $this->connect->deleteVotesByAdminPollId($poll_id);
    }

    /**
     * Delete a slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $slot string The name of the slot
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
    }

    public function addSlot($poll_id, $newdate, $newmoment) {
        $ex = explode('/', $newdate);
        $datetime = mktime(0,0,0, $ex[1], $ex[0], $ex[2]);

        $slot = $this->connect->findSlotByPollIdAndDatetime($poll_id, $datetime);

        if ($slot != null) {
            // Update found slot
            $moments = explode('@', $slot->sujet)[1];
            foreach ($moments as $moment) {
                if ($moment == $newmoment) {
                    return false;
                }
            }
            // TODO Implements

        } else {
            // TODO Found index of insertion, in order to update user votes
            $this->connect->insertSlot($poll_id, $datetime . '@' . $newmoment);
        }

        return true;

    }

}
 