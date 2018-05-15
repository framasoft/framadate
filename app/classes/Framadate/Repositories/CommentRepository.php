<?php
namespace Framadate\Repositories;

use Framadate\Utils;

class CommentRepository extends AbstractRepository {
    /**
     * @param $poll_id
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function findAllByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('comment') . ' WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Insert a new comment.
     *
     * @param $poll_id
     * @param $name
     * @param $comment
     * @return bool
     */
    function insert($poll_id, $name, $comment)
    {
        return $this->connect->insert(Utils::table('comment'), ['poll_id' => $poll_id, 'name' => $name, 'comment' => $comment]) > 0;
    }

    /**
     * @param $poll_id
     * @param $comment_id
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @return bool
     */
    function deleteById($poll_id, $comment_id)
    {
        return $this->connect->delete(Utils::table('comment'), ['poll_id' => $poll_id, 'id' => $comment_id]) > 0;
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     * @return bool true if action succeeded.
     */
    function deleteByPollId($poll_id)
    {
        return $this->connect->delete(Utils::table('comment'), ['poll_id' => $poll_id]) > 0;
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $comment
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function exists($poll_id, $name, $comment)
    {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('comment') . ' WHERE poll_id = ? AND name = ? AND comment = ?');
        $prepared->execute([$poll_id, $name, $comment]);

        return $prepared->rowCount() > 0;
    }
}
