<?php
namespace Framadate\Services;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
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

    function __construct(Connection $connect, LogService $logService) {
        $this->logService = $logService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    public function repeatedCleanings() {
    	$this->purgeOldPolls();
    	
    	if (0 === time() % 10) {
    		$this->cleanDemoPoll();
    	}
    }
    
    /**
     * This methode purges all old polls (the ones with end_date in past).
     *
     * @return bool true is action succeeded
     */
    public function purgeOldPolls() {
        try {
            $oldPolls = $this->pollRepository->findOldPolls();
            $count = count($oldPolls);

            if ($count > 0) {
                $this->logService->log('EXPIRATION', 'Going to purge ' . $count . ' poll(s)...');

                foreach ($oldPolls as $poll) {
                    if ($this->purgePollById($poll->id)) {
                        $this->logService->log(
                            'EXPIRATION_SUCCESS',
                            'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name
                        );
                    } else {
                        $this->logService->log(
                            'EXPIRATION_FAILED',
                            'id: ' . $poll->id . ', title:' . $poll->title . ', format: ' . $poll->format . ', admin: ' . $poll->admin_name
                        );
                    }
                }
            }

            return $count;
        } catch (DBALException $e) {
            $this->logService->log('ERROR', $e->getMessage());
            return false;
        }
    }
    
    public function cleanDemoPoll() {
    	if (!defined("DEMO_POLL_ID") || !defined("DEMO_POLL_NUMBER_VOTES")) {
    		return;
    	}
    	
    	$this->voteRepository->beginTransaction();
    	
    	$demoVotes = $this->voteRepository->allUserVotesByPollId(DEMO_POLL_ID);
    	$votesToDelete = count($demoVotes) - DEMO_POLL_NUMBER_VOTES;
    	
    	if ($votesToDelete > 0) {
    		$this->voteRepository->deleteOldVotesByPollId(DEMO_POLL_ID, $votesToDelete);
    	}
    	
    	$this->voteRepository->commit();
    }
    
    /**
     * This methode delete all data about a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    private function purgePollById($poll_id) {
        $done = true;

        try {
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
        } catch (DBALException $e) {
            $this->logService->log('ERROR', $e->getMessage());
        }

        return $done;
    }
}
