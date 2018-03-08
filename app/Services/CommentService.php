<?php
namespace Framadate\Services;

use Framadate\Repositories\CommentRepository;

/**
 * Class Comment
 *
 * @package Framadate\Services
 */
class CommentService {

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var LogService
     */
    private $logService;

    function __construct(CommentRepository $commentRepository, LogService $logService) {
        $this->commentRepository = $commentRepository;
        $this->logService = $logService;
    }

    /**
     * Delete a comment from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $comment_id int The ID of the comment
     * @return mixed true is action succeeded
     */
    function deleteComment($poll_id, $comment_id) {
        return $this->commentRepository->deleteById($poll_id, $comment_id);
    }

    /**
     * Remove all comments of a poll.
     *
     * @param $poll_id int The ID a the poll
     * @return bool|null true is action succeeded
     */
    function cleanComments($poll_id) {
        $this->logService->log("CLEAN_COMMENTS", "id:$poll_id");
        return $this->commentRepository->deleteByPollId($poll_id);
    }
}
