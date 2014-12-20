<?php
namespace Framadate\Services;

/**
 * Class AdminPollService
 * @package Framadate\Services
 */
class AdminPollService {

    private $connect;

    function __construct($connect) {
        $this->connect = $connect;
    }

    function updatePoll($poll) {
        return $this->connect->updatePoll($poll);
    }

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
        return $this->connect->deleteCommentssByAdminPollId($poll_id);
    }

    /**
     * Remove all votes of a poll.
     *
     * @param $poll_id int The ID a the poll
     * @return bool|null true is action succeeded
     */
    function cleanVotes($poll_id) {
        return $this->connect->deleteVotesByAdminPollId($poll_id);
    }

}
 