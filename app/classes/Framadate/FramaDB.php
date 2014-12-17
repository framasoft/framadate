<?php
namespace Framadate;

class FramaDB
{
    /**
     * PDO Object, connection to database.
     */
    private $pdo = null;

    function __construct($connection_string, $user, $password)
    {
        $this->pdo = new \PDO($connection_string, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    function areTablesCreated()
    {
        $result= $this->pdo->query('SHOW TABLES');
        $schemas = $result->fetchAll(\PDO::FETCH_COLUMN);
        return !empty(array_diff($schemas, ['comments', 'sondage', 'sujet_studs', 'user_studs']));
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

    function updateVote($poll_id, $vote_id, $choices) {
        $prepared = $this->prepare('UPDATE user_studs SET reponses = ? WHERE id_sondage = ? AND id_users = ?');
        return $prepared->execute([$choices, $poll_id, $vote_id]);
    }

    function insertComment($poll_id, $name, $comment) {
        $prepared = $this->prepare('INSERT INTO comments (id_sondage, usercomment, comment) VALUES (?,?,?)');
        return $prepared->execute([$poll_id, $name, $comment]);
    }

}
