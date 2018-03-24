<?php
namespace Framadate\Repository;

use Framadate\Entity\Comment;
use Framadate\Utils;

class CommentRepository extends AbstractRepository
{

    /**
     * @param $poll_id
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAllByPollId($poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('comment') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Insert a new comment.
     *
     * @param Comment $comment
     * @return Comment $comment
     */
    public function insert(Comment $comment)
    {
        $comment->setCreatedAt(new \DateTime());
        $this->connect->insert(Utils::table('comment'), [
            'poll_id' => $comment->getPollId(),
            'name' => $comment->getName(),
            'comment' => $comment->getContent(),
            'date' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);

        return $comment->setId(intval($this->lastInsertId()));
    }

    /**
     * @param $poll_id
     * @param $comment_id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteById($poll_id, $comment_id)
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $comment_id]);
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param string $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByPollId(string $poll_id)
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * @param Comment $comment
     * @return Comment|bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function exists(Comment $comment)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND name = ? AND comment = ?');
        $prepared->execute([$comment->getPollId(), $comment->getName(), $comment->getContent()]);

        if ($prepared->rowCount() > 0) {
            return $this->dataToComment($prepared->fetch());
        }
        return false;
    }

    /**
     * @param array $data
     * @return Comment
     */
    private function dataToComment($data)
    {
        $comment = new Comment();
        return $comment->setId($data['id'])
            ->setName($data['name'])
            ->setContent($data['comment'])
            ->setPollId($data['poll_id'])
            ->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data['date']))
            ;
    }
}
