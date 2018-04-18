<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class VoteRepository extends AbstractRepository {

    /**
     * @param $poll_id
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function allUserVotesByPollId($poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('vote') . ' WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    /**
     * @param $poll_id
     * @param $insert_position
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertDefault($poll_id, $insert_position)
    {
        # TODO : Handle this on PHP's side
        $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?'); //#51 : default value for unselected vote

        return $prepared->execute([$insert_position, $insert_position + 1, $poll_id]);
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $choices
     * @param $token
     * @return \stdClass
     */
    public function insert($poll_id, $name, $choices, $token)
    {
        $this->connect->insert(Utils::table('vote'), ['poll_id' => $poll_id, 'name' => $name, 'choices' => $choices, 'uniqId' => $token]);

        $newVote = new \stdClass();
        $newVote->poll_id = $poll_id;
        $newVote->id = $this->lastInsertId();
        $newVote->name = $name;
        $newVote->choices = $choices;
        $newVote->uniqId = $token;

        return $newVote;
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
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
     * @return bool|null true if action succeeded.
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByIndex($poll_id, $index)
    {
        $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?');

        return $prepared->execute([$index, $index + 2, $poll_id]);
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @param $name
     * @param $choices
     * @return bool
     */
    public function update($poll_id, $vote_id, $name, $choices)
    {
        return $this->connect->update(Utils::table('vote'), [
            'choices' => $choices,
            'name' => $name,
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
     * @return bool true if vote already exists
     * @throws \Doctrine\DBAL\DBALException
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
     * @return bool true if vote already exists
     * @throws \Doctrine\DBAL\DBALException
     */
    public function existsByPollIdAndNameAndVoteId($poll_id, $name, $vote_id) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND name = ? AND id != ?');
        $prepared->execute([$poll_id, $name, $vote_id]);
        return $prepared->rowCount() > 0;
    }
}

