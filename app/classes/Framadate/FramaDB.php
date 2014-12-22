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

    function areTablesCreated() {
        $result = $this->pdo->query('SHOW TABLES');
        $schemas = $result->fetchAll(\PDO::FETCH_COLUMN);
        return 0 != count(array_diff($schemas, ['comments', 'sondage', 'sujet_studs', 'user_studs']));
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

    function query($sql) {
        return $this->pdo->query($sql);
    }

    function findPollById($poll_id) {
        $prepared = $this->prepare('SELECT * FROM sondage WHERE sondage.poll_id = ?');

        $prepared->execute([$poll_id]);
        $poll = $prepared->fetch();
        $prepared->closeCursor();

        return $poll;
    }

    function updatePoll($poll) {
        $prepared = $this->prepare('UPDATE sondage SET title=?, admin_mail=?, comment=?, active=?, editable=? WHERE sondage.poll_id = ?');

        return $prepared->execute([$poll->title, $poll->admin_mail, $poll->comment, $poll->active, $poll->editable, $poll->poll_id]);
    }

    function allCommentsByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM comments WHERE id_sondage = ? ORDER BY id_comment');
        $prepared->execute(array($poll_id));
        return $prepared->fetchAll();
    }

    function allUserVotesByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM user_studs WHERE id_sondage = ? ORDER BY id_users');
        $prepared->execute(array($poll_id));
        return $prepared->fetchAll();
    }

    function allSlotsByPollId($poll_id) {
        $prepared = $this->prepare('SELECT * FROM sujet_studs WHERE id_sondage = ? ORDER BY sujet');
        $prepared->execute(array($poll_id));
        return $prepared->fetchAll();
    }

    function insertVote($poll_id, $name, $choices) {
        $prepared = $this->prepare('INSERT INTO user_studs (id_sondage,nom,reponses) VALUES (?,?,?)');
        $prepared->execute([$poll_id, $name, $choices]);

        $newVote = new \stdClass();
        $newVote->id_sondage = $poll_id;
        $newVote->id_users = $this->pdo->lastInsertId();
        $newVote->nom = $name;
        $newVote->reponse = $choices;

        return $newVote;
    }

    function deleteVote($poll_id, $vote_id) {
        $prepared = $this->prepare('DELETE FROM user_studs WHERE id_sondage = ? AND id_users = ?');
        return $prepared->execute([$poll_id, $vote_id]);
    }

    /**
     * Delete all votes of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteVotesByAdminPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM user_studs WHERE id_sondage = ?');
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
        $prepared = $this->prepare('UPDATE user_studs SET reponses = CONCAT(SUBSTR(reponses, 1, ?), SUBSTR(reponses, ?)) WHERE id_sondage = ?');
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
        $prepared = $this->prepare('SELECT * FROM sujet_studs WHERE id_sondage = ? AND SUBSTRING_INDEX(sujet, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $slot = $prepared->fetch();
        $prepared->closeCursor();

        return $slot;
    }

    /**
     * Insert a new slot into a given poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $slot mixed The value of the slot
     * @return bool true if action succeeded
     */
    function insertSlot($poll_id, $slot) {
        $prepared = $this->prepare('INSERT INTO sujet_studs (id_sondage, sujet) VALUES (?,?)');
        return $prepared->execute([$poll_id, $slot]);
    }

    /**
     * Update a slot into a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the slot to update
     * @param $newValue mixed The new value of the entire slot
     * @return bool|null true if action succeeded.
     */
    function updateSlot($poll_id, $datetime, $newValue) {
        $prepared = $this->prepare('UPDATE sujet_studs SET sujet = ? WHERE id_sondage = ? AND SUBSTRING_INDEX(sujet, \'@\', 1) = ?');
        return $prepared->execute([$newValue, $poll_id, $datetime]);
    }

    /**
     * Delete a entire slot from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime mixed The datetime of the slot
     */
    function deleteSlot($poll_id, $datetime) {
        $prepared = $this->prepare('DELETE FROM sujet_studs WHERE id_sondage = ? AND SUBSTRING_INDEX(sujet, \'@\', 1) = ?');
        $prepared->execute([$poll_id, $datetime]);
    }

    /**
     * Delete all comments of a given poll.
     *
     * @param $poll_id int The ID of the given poll.
     * @return bool|null true if action succeeded.
     */
    function deleteCommentsByAdminPollId($poll_id) {
        $prepared = $this->prepare('DELETE FROM comments WHERE id_sondage = ?');
        return $prepared->execute([$poll_id]);
    }

    function updateVote($poll_id, $vote_id, $choices) {
        $prepared = $this->prepare('UPDATE user_studs SET reponses = ? WHERE id_sondage = ? AND id_users = ?');
        return $prepared->execute([$choices, $poll_id, $vote_id]);
    }

    function insertComment($poll_id, $name, $comment) {
        $prepared = $this->prepare('INSERT INTO comments (id_sondage, usercomment, comment) VALUES (?,?,?)');
        return $prepared->execute([$poll_id, $name, $comment]);
    }

    function deleteComment($poll_id, $comment_id) {
        $prepared = $this->prepare('DELETE FROM comments WHERE id_sondage = ? AND id_comment = ?');
        return $prepared->execute([$poll_id, $comment_id]);
    }

}
