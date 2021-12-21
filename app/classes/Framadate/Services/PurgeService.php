<?php
namespace Framadate\Services;
use Framadate\FramaDB;
use Framadate\Repositories\RepositoryFactory;

/**
 * This service helps to purge data.
 *
 * @package Framadate\Services
 */
class PurgeService {
    private $logService;
    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(LogService $logService) {
        $this->logService = $logService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @return int number of purged polls
     */
    public function purgeOldPolls(): int
    {
        $oldPolls = $this->pollRepository->findOldPolls();
        $count = count($oldPolls);

        if ($count > 0) {
            $this->logService->log('EXPIRATION', 'Going to purge ' . $count . ' poll(s)...');

            foreach ($oldPolls as $poll) {
                if ($this->purgePollById($poll->id)) {
                    $this->logService->log('EXPIRATION_SUCCESS', 'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name);
                } else {
                    $this->logService->log('EXPIRATION_FAILED', 'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name);
                }
            }
        }

        return $count;
    }

    /**
     * This methode delete all data about a poll.
     *
     * @param string $poll_id The ID of the poll
     * @return bool true is action succeeded
     */
    public function purgePollById(string $poll_id): bool
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
