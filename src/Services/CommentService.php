<?php
namespace Framadate\Services;

use Doctrine\DBAL\DBALException;
use Framadate\Repository\CommentRepository;
use Psr\Log\LoggerInterface;

/**
 * Class Comment
 *
 * @package Framadate\Services
 */
class CommentService
{

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CommentRepository $commentRepository, LoggerInterface $logger)
    {
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
    }

    /**
     * Delete a comment from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $comment_id int The ID of the comment
     * @return mixed true is action succeeded
     */
    public function deleteComment($poll_id, $comment_id)
    {
        return $this->commentRepository->deleteById($poll_id, $comment_id);
    }

    /**
     * Remove all comments of a poll.
     *
     * @param $poll_id int The ID a the poll
     * @return bool|null true is action succeeded
     */
    public function cleanComments($poll_id)
    {
        $this->logger->info("CLEAN_COMMENTS for poll ID " . $poll_id);
        try {
            return $this->commentRepository->deleteByPollId($poll_id);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }
}
