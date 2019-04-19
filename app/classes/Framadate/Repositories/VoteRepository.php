<?php
namespace Framadate\Repositories;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use Framadate\Utils;

class VoteRepository extends AbstractRepository {
    /**
     * @param $poll_id
     * @throws DBALException
     * @return array
     */
    public function allUserVotesByPollId($poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('vote') . ' WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * Insert default values for slots
     *
     * @param string $poll_id
     * @param integer $insert_position
     * @return Statement|int
     */
    public function insertDefault($poll_id, $insert_position)
    {
        $qb = $this->createQueryBuilder();
        $sql = $this->connect->getDatabasePlatform()->getName() === 'sqlite' ?
            $this->generateSQLForInsertDefaultSQLite($qb, $insert_position) :
            $this->generateSQLForInsertDefault($qb, $insert_position)
            ;
        $query = $qb->update(Utils::table('vote'))
            ->set('choices', $sql)
            ->where($qb->expr()->eq('poll_id', $qb->createNamedParameter($poll_id)))
        ;
        return $query->execute();
    }

    function insert($poll_id, $name, $choices, $token, $mail) {
        $this->connect->insert(Utils::table('vote'), ['poll_id' => $poll_id, 'name' => $name, 'choices' => $choices, 'uniqId' => $token, 'mail' => $mail]);

        $newVote = new \stdClass();
        $newVote->poll_id = $poll_id;
        $newVote->id = $this->lastInsertId();
        $newVote->name = $name;
        $newVote->choices = $choices;
        $newVote->uniqId = $token;
	    $newVote->mail=$mail;

        return $newVote;
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @throws DBALException
     * @return bool
     */
    public function deleteById($poll_id, $vote_id)
    {
        return $this->connect->delete(Utils::table('vote'), ['poll_id' => $poll_id, 'id' => $vote_id]) > 0;
    }

    public function deleteOldVotesByPollId($poll_id, $votesToDelete) {
    	$prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ? ORDER BY `poll_id` ASC LIMIT ' . $votesToDelete);

        return $prepared->execute([$poll_id]);
    }

    /**
     * Delete all votes of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @throws DBALException
     * @return bool|null true if action succeeded.
     */
    public function deleteByPollId($poll_id)
    {
        return $this->connect->delete(Utils::table('vote'), ['poll_id' => $poll_id]) > 0;
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param $poll_id int The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    public function deleteByIndex($poll_id, $index)
    {
        $qb = $this->createQueryBuilder();
        $sql = $this->connect->getDatabasePlatform()->getName() === 'sqlite' ?
            $this->generateSQLForInsertDefaultSQLite($qb, $index, true) :
            $this->generateSQLForInsertDefault($qb, $index, true)
        ;
        $query = $qb->update(Utils::table('vote'))
            ->set('choices', $sql)
            ->where($qb->expr()->eq('poll_id', $qb->createNamedParameter($poll_id)))
        ;
        return $query->execute();
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @param $name
     * @param $choices
     * @return bool
     */
    public function update($poll_id, $vote_id, $name, $choices, $mail)
    {
        return $this->connect->update(Utils::table('vote'), [
            'choices' => $choices,
            'name' => $name,
            'mail' => $mail,
        ], [
            'poll_id' => $poll_id,
            'id' => $vote_id,
        ]) > 0;
    }

    /**
     * Check if name is already used for the given poll.
     *
     * @param int $poll_id ID of the poll
     * @param string $name Name of the vote
     * @throws DBALException
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndName($poll_id, $name) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND name = ?');
        $prepared->execute([$poll_id, $name]);
        return $prepared->rowCount() > 0;
    }

    /**
     * Check if name is already used for the given poll and another vote.
     *
     * @param int $poll_id ID of the poll
     * @param string $name Name of the vote
     * @param int $vote_id ID of the current vote
     * @throws DBALException
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndNameAndVoteId($poll_id, $name, $vote_id) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND name = ? AND id != ?');
        $prepared->execute([$poll_id, $name, $vote_id]);
        return $prepared->rowCount() > 0;
    }

    /**
     * @param QueryBuilder $qb
     * @param $insert_position
     * @param bool $delete
     * @return string
     */
    private function generateSQLForInsertDefaultSQLite($qb, $insert_position, $delete = false)
    {
        $position = $insert_position + ($delete ? 2 : 1);
        return 'SUBSTR(choices, 1, '
        . $qb->createNamedParameter($insert_position)
        . ') || " " || SUBSTR(choices, '
            . $qb->createNamedParameter($position)
        . ')';
    }

    /**
     * @param QueryBuilder $qb
     * @param int $insert_position
     * @param bool $delete
     * @return string
     */
    private function generateSQLForInsertDefault($qb, $insert_position, $delete = false)
    {
        $position = $insert_position + ($delete ? 2 : 1);
        return 'CONCAT(SUBSTR(choices, 1, '
            . $qb->createNamedParameter($insert_position)
            . '), " ", SUBSTR(choices, '
            . $qb->createNamedParameter($position)
            . '))';
    }
}

