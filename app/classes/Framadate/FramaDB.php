<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate;

use PDO;

class FramaDB {
    /**
     * PDO Object, connection to database.
     */
    private $pdo = null;

    function __construct($connection_string, $user, $password) {
        $this->pdo = new \PDO($connection_string, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @return \PDO Connection to database
     */
    function getPDO() {
        return $this->pdo;
    }

    /**
     * Find all tables in database.
     *
     * @return array The array of table names
     */
    function allTables() {
        $result = $this->pdo->query('SHOW TABLES');
        $schemas = $result->fetchAll(\PDO::FETCH_COLUMN);

        return $schemas;
    }

    function prepare($sql) {
        return $this->pdo->prepare($sql);
    }

    function beginTransaction() {
        $this->pdo->beginTransaction();
    }

    function commit() {
        $this->pdo->commit();
    }

    function rollback() {
        $this->pdo->rollback();
    }

    function errorCode() {
        return $this->pdo->errorCode();
    }

    function errorInfo() {
        return $this->pdo->errorInfo();
    }

    function query($sql) {
        return $this->pdo->query($sql);
    }

    function findPollById($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute([$poll_id]);
        $poll = $prepared->fetch();
        $prepared->closeCursor();

        return $poll;
    }

    function updatePoll($poll) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('poll') . '` SET title=?, admin_name=?, admin_mail=?, description=?, end_date=FROM_UNIXTIME(?), active=?, editable=? WHERE id = ?');

        return $prepared->execute([$poll->title, $poll->admin_name, $poll->admin_mail, $poll->description, $poll->end_date, $poll->active, $poll->editable, $poll->id]);
    }

    function allCommentsByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('comment') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute(array($poll_id));

        return $prepared->fetchAll();
    }

    function allUserVotesByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('vote') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute(array($poll_id));

        return $prepared->fetchAll();
    }

    function allSlotsByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? ORDER BY title');
        $prepared->execute(array($poll_id));

        return $prepared->fetchAll();
    }

    function insertDefaultVote($poll_id, $insert_position) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTRING(choices, 1, ?), "0", SUBSTRING(choices, ?)) WHERE poll_id = ?');

        return $prepared->execute([$insert_position, $insert_position + 1, $poll_id]);
    }

    function insertVote($poll_id, $name, $choices) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('vote') . '` (poll_id, name, choices) VALUES (?,?,?)');
        $prepared->execute([$poll_id, $name, $choices]);

        $newVote = new \stdClass();
        $newVote->poll_id = $poll_id;
        $newVote->id = $this->pdo->lastInsertId();
        $newVote->name = $name;
        $newVote->choices = $choices;

        return $newVote;
    }

    function deleteVote($poll_id, $vote_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $vote_id]);
    }

    /**
     * Delete all votes of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteVotesByPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('vote') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Delete all votes made on given moment index.
     *
     * @param $poll_id int The ID of the poll
     * @param $index int The index of the vote into the poll
     * @return bool|null true if action succeeded.
     */
    function deleteVotesByIndex($poll_id, $index) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = CONCAT(SUBSTR(choices, 1, ?), SUBSTR(choices, ?)) WHERE poll_id = ?');

        return $prepared->execute([$index, $index + 2, $poll_id]);
    }

    /**
     * Find the slot into poll for a given datetime.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot
     * @return mixed Object The slot found, or null
     */
    function findSlotByPollIdAndDatetime($poll_id, $datetime) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND SUBSTRING_INDEX(title, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $slot = $prepared->fetch();
        $prepared->closeCursor();

        return $slot;
    }

    /**
     * Insert a new slot into a given poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $title mixed The title of the slot
     * @param $moments mixed|null The moments joined with ","
     * @return bool true if action succeeded
     */
    function insertSlot($poll_id, $title, $moments) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('slot') . '` (poll_id, title, moments) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $title, $moments]);
    }

    /**
     * Update a slot into a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot to update
     * @param $newMoments mixed The new moments
     * @return bool|null true if action succeeded.
     */
    function updateSlot($poll_id, $datetime, $newMoments) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('slot') . '` SET moments = ? WHERE poll_id = ? AND title = ?');

        return $prepared->execute([$newMoments, $poll_id, $datetime]);
    }

    /**
     * Delete a entire slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime mixed The datetime of the slot
     */
    function deleteSlot($poll_id, $datetime) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND title = ?');
        $prepared->execute([$poll_id, $datetime]);
    }

    function deleteSlotsByPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('slot') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteCommentsByPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ?');

        return $prepared->execute([$poll_id]);
    }

    function updateVote($poll_id, $vote_id, $name, $choices) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('vote') . '` SET choices = ?, name = ? WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$choices, $name, $poll_id, $vote_id]);
    }

    function insertComment($poll_id, $name, $comment) {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('comment') . '` (poll_id, name, comment) VALUES (?,?,?)');

        return $prepared->execute([$poll_id, $name, $comment]);
    }

    function deleteComment($poll_id, $comment_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('comment') . '` WHERE poll_id = ? AND id = ?');

        return $prepared->execute([$poll_id, $comment_id]);
    }

    function deletePollById($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('poll') . '` WHERE id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Find old polls. Limit: 20.
     *
     * @return array Array of old polls
     */
    public function findOldPolls() {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE DATE_ADD(`end_date`, INTERVAL ' . PURGE_DELAY . ' DAY) < NOW() AND `end_date` != 0 LIMIT 20');
        $prepared->execute([]);

        return $prepared->fetchAll();
    }

    /**
     * @param $start int The index of the first poll to return
     * @param $limit int The limit size
     * @return array
     */
    public function findAllPolls($start, $limit) {
        // Polls
        $prepared = $this->prepare('
SELECT p.*,
       (SELECT count(1) FROM `' . Utils::table('vote') . '` v WHERE p.id=v.poll_id) votes
  FROM ' . Utils::table('poll') . ' p
 ORDER BY p.title ASC
 LIMIT :start, :limit');
        $prepared->bindParam(':start', $start, PDO::PARAM_INT);
        $prepared->bindParam(':limit', $limit, PDO::PARAM_INT);
        $prepared->execute();
        $polls = $prepared->fetchAll();

        // Total count
        $stmt = $this->query('SELECT count(1) nb FROM `' . Utils::table('poll') . '`');
        $count = $stmt->fetch();
        $stmt->closeCursor();

        return ['polls' => $polls, 'count' => $count->nb];
    }

    public function countVotesByPollId($poll_id) {
        $prepared = $this->prepare('SELECT count(1) nb FROM `' . Utils::table('vote') . '` WHERE poll_id = ?');

        $prepared->execute([$poll_id]);
        $result = $prepared->fetch();
        $prepared->closeCursor();

        return $result->nb;
    }
}
