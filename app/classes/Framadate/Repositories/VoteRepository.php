<?php
namespace Framadate\Repositories;

use Framadate\Utils;

class VoteRepository extends AbstractRepository {
    /**
     * @param $poll_id
     * @throws \Doctrine\DBAL\DBALException
     * @return array
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
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function insertDefault($poll_id, $insert_position)
    {
        # TODO : Handle this on PHP's side
        $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?'); //#51 : default value for unselected vote

        return $prepared->execute([$insert_position, $insert_position + 1, $poll_id]);
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
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
     * @return bool|null true if action succeeded.
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
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndNameAndVoteId($poll_id, $name, $vote_id) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND name = ? AND id != ?');
        $prepared->execute([$poll_id, $name, $vote_id]);
        return $prepared->rowCount() > 0;
    }
}

