<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class VoteRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

   function allUserVotesByPollId($poll_id) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('SELECT * FROM `' . Utils::table('vote') . '` WHERE poll_id = ? ORDER BY id');
                break;
            case 'pgsql':
                $prepared = $this->prepare('SELECT * FROM ' . Utils::table('vote') . ' WHERE poll_id = ? ORDER BY id');
                break;
        }

        $prepared->execute(array($poll_id));

        return $prepared->fetchAll();
    }

    function insertDefault($poll_id, $insert_position) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?'); //#51 : default value for unselected vote
                break;
            case 'pgsql':
                $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = CONCAT(SUBSTRING(choices, 1, ?), " ", SUBSTRING(choices, ?)) WHERE poll_id = ?'); //#51 : default value for unselected vote
                break;
        }
        return $prepared->execute([$insert_position, $insert_position + 1, $poll_id]);
    }

    function insert($poll_id, $name, $choices, $token) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('INSERT INTO `' . Utils::table('vote') . '` (poll_id, name, choices, uniqId) VALUES (?,?,?,?)');
                break;
            case 'pgsql':
                $prepared = $this->prepare('INSERT INTO ' . Utils::table('vote') . ' (poll_id, name, choices, uniqId) VALUES (?,?,?,?)');
                break;
        }
        $prepared->execute([$poll_id, $name, $choices, $token]);

        $newVote = new \stdClass();
        $newVote->poll_id = $poll_id;
        $newVote->id = $this->lastInsertId();
        $newVote->name = $name;
        $newVote->choices = $choices;
        $newVote->uniqId = $token;

        return $newVote;
    }

    function deleteById($poll_id, $vote_id) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND id = ?');
            case 'pgsql':
                $prepared = $this->prepare('DELETE FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND id = ?');
                break;
        }

        return $prepared->execute([$poll_id, $vote_id]);
    }

    /**
     * Delete all votes of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteByPollId($poll_id) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ?');
                break;
            case 'pgsql':
                $prepared = $this->prepare('DELETE FROM ' . Utils::table('vote') . ' WHERE poll_id = ?');
                break;
        }

        return $prepared->execute([$poll_id]);
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param $poll_id int The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    function deleteByIndex($poll_id, $index) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?');
                break;
            case 'pgsql':
                $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?');
                break;
        }

        return $prepared->execute([$index, $index + 2, $poll_id]);
    }

    function update($poll_id, $vote_id, $name, $choices) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = ?, name = ? WHERE poll_id = ? AND id = ?');
                break;
            case 'pgsql':
                $prepared = $this->prepare('UPDATE ' . Utils::table('vote') . ' SET choices = ?, name = ? WHERE poll_id = ? AND id = ?');
                break;
        }

        return $prepared->execute([$choices, $name, $poll_id, $vote_id]);
    }

    /**
     * Check if name is already used for the given poll.
     *
     * @param int $poll_id ID of the poll
     * @param string $name Name of the vote
     * @return bool true if vote already exists
     */
    public function existsByPollIdAndName($poll_id, $name) {
        switch (DB_DRIVER_NAME) {
            case 'mysql':
                $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND name = ?');
                break;
            case 'pgsql':
                $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('vote') . ' WHERE poll_id = ? AND name = ?');
                break;
        }

        $prepared->execute(array($poll_id, $name));
        return $prepared->rowCount() > 0;
    }
}
