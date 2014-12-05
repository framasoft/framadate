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

}
