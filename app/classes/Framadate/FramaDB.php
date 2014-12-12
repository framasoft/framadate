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

    function findPollById($poll_id)
    {

        // Open database
        if (preg_match(';^[\w\d]{16}$;i', $poll_id)) {
            $prepared = $this->prepare('SELECT * FROM sondage WHERE sondage.poll_id = ?');

            $prepared->execute([$poll_id]);
            $poll = $prepared->fetch();
            $prepared->closeCursor();

            return $poll;
        }

        return null;
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

    function insertVote($name, $poll_id, $votes) {
        $prepared = $this->prepare('INSERT INTO user_studs (nom,id_sondage,reponses) VALUES (?,?,?)');
        $prepared->execute([$name, $poll_id, $votes]);

        $newVote = new \stdClass();
        $newVote->id_sondage = $poll_id;
        $newVote->id_users = $this->pdo->lastInsertId();
        $newVote->nom = $name;
        $newVote->reponse = $votes;

        return $newVote;
    }

}
