<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class VoteRepository extends AbstractRepository {
    /**
     * @return array|false
     */
    public function allUserVotesByPollId(string $poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('vote') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return $prepared->fetchAll();
    }

    public function insertDefault(string $poll_id, int $insert_position): bool
    {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?'); //#51 : default value for unselected vote

        return $prepared->execute([$insert_position, $insert_position + 1, $poll_id]);
    }

    public function insert(string $poll_id, string $name, string $choices, string $token): \stdClass {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('vote') . '` (poll_id, name, choices, uniqId) VALUES (?,?,?,?)');
        $prepared->execute([$poll_id, $name, $choices, $token]);

        $newVote = new \stdClass();
        $newVote->poll_id = $poll_id;
        $newVote->id = $this->lastInsertId();
        $newVote->name = $name;
        $newVote->choices = $choices;
        $newVote->uniqId = $token;

        return $newVote;
    }

    public function deleteById(string $poll_id, int $vote_id): bool
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $vote_id]);
    }

    /**
     * Delete all votes of a given poll.
     *
     * @param string $poll_id The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    public function deleteByPollId(string $poll_id): ?bool
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param string $poll_id The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    public function deleteByIndex(string $poll_id, int $index): ?bool
    {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?');

        return $prepared->execute([$index, $index + 2, $poll_id]);
    }

    public function update(string $poll_id, string $vote_id, string $name, $choices): bool
    {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = ?, name = ? WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$choices, $name, $poll_id, $vote_id]);
    }

    /**
     * Check if name is already used for the given poll.
     *
     * @param string $poll_id ID of the poll
     * @param string $name Name of the vote
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndName(string $poll_id, string $name): bool
    {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND name = ?');
        $prepared->execute([$poll_id, $name]);
        return $prepared->rowCount() > 0;
    }

    /**
     * Check if name is already used for the given poll and another vote.
     *
     * @param string $poll_id ID of the poll
     * @param string $name Name of the vote
     * @param int $vote_id ID of the current vote
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndNameAndVoteId(string $poll_id, string $name, int $vote_id): bool
    {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND name = ? AND id != ?');
        $prepared->execute([$poll_id, $name, $vote_id]);
        return $prepared->rowCount() > 0;
    }
}

