<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class CommentRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

    function findAllByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('comment') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute(array($poll_id));

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
    function insert($poll_id, $name, $comment) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('comment') . '` (poll_id, name, comment) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $name, $comment]);
    }

    function deleteById($poll_id, $comment_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $comment_id]);
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteByPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    public function exists($poll_id, $name, $comment) {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND name = ? AND comment = ?');
        $prepared->execute(array($poll_id, $name, $comment));

        return $prepared->rowCount() > 0;
    }

}
