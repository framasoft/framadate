<?php
namespace Framadate\Services;

use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
use Framadate\Repository\SlotRepository;
use Framadate\Repository\VoteRepository;

/**
 * This service helps to purge data.
 *
 * @package Framadate\Services
 */
class PurgeService
{
    private $logService;
    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(LogService $logService, PollRepository $pollRepository, SlotRepository $slotRepository, VoteRepository $voteRepository, CommentRepository $commentRepository)
    {
        $this->logService = $logService;
        $this->pollRepository = $pollRepository;
        $this->slotRepository = $slotRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @param $purge_delay
     * @return bool true is action succeeded
     */
    public function purgeOldPolls($purge_delay)
    {
        $oldPolls = $this->pollRepository->findOldPolls($purge_delay);
        $count = count($oldPolls);

        if ($count > 0) {
            // $this->logService->log('EXPIRATION', 'Going to purge ' . $count . ' poll(s)...');

            foreach ($oldPolls as $poll) {
                if ($this->purgePollById($poll->getId())) {
                    // $this->logService->log('EXPIRATION_SUCCESS', 'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name);
                } else {
                    // $this->logService->log('EXPIRATION_FAILED', 'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name);
                }
            }
        }

        return $count;
    }

    /**
     * This methode delete all data about a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    public function purgePollById($poll_id)
    {
        $done = true;

        $this->pollRepository->beginTransaction();
        $done &= $this->commentRepository->deleteByPollId($poll_id);
        $done &= $this->voteRepository->deleteByPollId($poll_id);
        $done &= $this->slotRepository->deleteByPollId($poll_id);
        $done &= $this->pollRepository->deleteById($poll_id);

        if ($done) {
            $this->pollRepository->commit();
        } else {
            $this->pollRepository->rollback();
        }

        return $done;
    }
}
