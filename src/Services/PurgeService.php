<?php
namespace Framadate\Services;

use Doctrine\DBAL\DBALException;
use Framadate\Repository\ChoiceRepository;
use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
use Framadate\Repository\VoteRepository;
use Psr\Log\LoggerInterface;

/**
 * This service helps to purge data.
 *
 * @package Framadate\Services
 */
class PurgeService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    private $pollRepository;
    private $choiceRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(LoggerInterface $logger, PollRepository $pollRepository, ChoiceRepository $choiceRepository, VoteRepository $voteRepository, CommentRepository $commentRepository)
    {
        $this->logger = $logger;
        $this->pollRepository = $pollRepository;
        $this->choiceRepository = $choiceRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @param $purge_delay
     * @return bool true is action succeeded
     * @throws \Doctrine\DBAL\DBALException
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

        try {
            $this->pollRepository->beginTransaction();
            $done &= $this->commentRepository->deleteByPollId($poll_id);
            $done &= $this->voteRepository->deleteByPollId($poll_id);
            $done &= $this->choiceRepository->deleteByPollId($poll_id);
            $done &= $this->pollRepository->deleteById($poll_id);

            if ($done) {
                $this->pollRepository->commit();
            } else {
                $this->pollRepository->rollback();
            }
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return $done;
    }
}
