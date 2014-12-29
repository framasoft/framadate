<?php
namespace Framadate\Services;
use Framadate\FramaDB;

/**
 * This service helps to purge data.
 *
 * @package Framadate\Services
 */
class PurgeService {

    private $connect;
    private $logService;

    function __construct(FramaDB $connect, LogService $logService) {
        $this->connect = $connect;
        $this->logService = $logService;
    }

    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @return bool true is action succeeded
     */
    function purgeOldPolls() {
        $oldPolls = $this->connect->findOldPolls();
        $count = count($oldPolls);

        if ($count > 0) {
            $this->logService->log('EXPIRATION', 'Going to purge ' . $count . ' poll(s)...');

            foreach ($oldPolls as $poll) {
                if ($this->purgePollById($poll->poll_id)) {
                    $this->logService->log('EXPIRATION_SUCCESS', 'id: ' . $poll->poll_id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                } else {
                    $this->logService->log('EXPIRATION_FAILED', 'id: ' . $poll->poll_id . ', title:' . $poll->title . ', format: '.$poll->format . ', admin: ' . $poll->admin_name);
                }
            }
        }

        return false;
    }

    /**
     * This methode delete all data about a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    function purgePollById($poll_id) {
        $done = false;
        $done |= $this->connect->deleteCommentsByPollId($poll_id);
        $done |= $this->connect->deleteVotesByPollId($poll_id);
        $done |= $this->connect->deleteSlotsByPollId($poll_id);
        $done |= $this->connect->deletePollById($poll_id);

        return $done;
    }

}
 