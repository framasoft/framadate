<?php
namespace Framadate\Repositories;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Type;
use Framadate\Utils;

class CommentRepository extends AbstractRepository {
    /**
     * @param $poll_id
     * @throws DBALException
     * @return array
     */
    public function findAllByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('comment') . ' WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        $comments = $prepared->fetchAll();

        /**
         * Hack to make date a proper DateTime
         */
        return array_map(function($comment) {
            $comment->date = Type::getType(Type::DATETIME)->convertToPhpValue($comment->date, $this->connect->getDatabasePlatform());
            return $comment;
        }, $comments);
    }

    /**
     * Insert a new comment.
     *
     * @param $poll_id
     * @param $name
     * @param $comment
     * @return bool
     */
    public function insert($poll_id, $name, $comment)
    {
        return $this->connect->insert(Utils::table('comment'), ['poll_id' => $poll_id, 'name' => $name, 'comment' => $comment]) > 0;
    }

    /**
     * @param $poll_id
     * @param $comment_id
     * @throws InvalidArgumentException
     * @return bool
     */
    public function deleteById($poll_id, $comment_id)
    {
        return $this->connect->delete(Utils::table('comment'), ['poll_id' => $poll_id, 'id' => $comment_id]) > 0;
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @throws InvalidArgumentException
     * @return bool true if action succeeded.
     */
    public function deleteByPollId($poll_id)
    {
        return $this->connect->delete(Utils::table('comment'), ['poll_id' => $poll_id]) > 0;
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $comment
     * @throws DBALException
     * @return bool
     */
    public function exists($poll_id, $name, $comment)
    {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('comment') . ' WHERE poll_id = ? AND name = ? AND comment = ?');
        $prepared->execute([$poll_id, $name, $comment]);

        return $prepared->rowCount() > 0;
    }
}
