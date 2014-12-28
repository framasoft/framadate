<?php
namespace Framadate\Services;
use Framadate\FramaDB;

/**
 * This service helps to purge old poll.
 *
 * @package Framadate\Services
 */
class PurgeService {

    private $connect;

    function __construct(FramaDB $connect) {
        $this->connect = $connect;
    }

    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @return bool true is action succeeded
     */
    function purgeOldPolls() {
        // TODO Implements
        return false;
    }

    /**
     * This methode delete all data about a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    function purgePollById($poll_id) {
        // TODO Implements
        return false;
    }

}
 