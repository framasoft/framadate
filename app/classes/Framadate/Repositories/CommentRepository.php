<?php
namespace Framadate\Repositories;

use Framadate\Utils;

class CommentRepository extends AbstractRepository {
    /**
     * @return array|false
     */
    public function findAllByPollId(string $poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('comment') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Insert a new comment.
     *
     * @param string $poll_id
     * @param string $name
     * @param string $comment
     * @return bool
     */
    public function insert(string $poll_id, string $name, string $comment): bool
    {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('comment') . '` (poll_id, name, comment) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $name, $comment]);
    }

    public function deleteById(string $poll_id, int $comment_id): bool
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $comment_id]);
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param string $poll_id The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    public function deleteByPollId(string $poll_id): ?bool
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    public function exists(string $poll_id, string $name, string $comment): bool
    {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND name = ? AND comment = ?');
        $prepared->execute([$poll_id, $name, $comment]);

        return $prepared->rowCount() > 0;
    }
}
