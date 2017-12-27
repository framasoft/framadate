<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;

abstract class AbstractRepository {

    /**
     * @var FramaDB
     */
    private $connect;

    /**
     * PollRepository constructor.
     * @param FramaDB $connect
     */
    function __construct(FramaDB $connect) {
        $this->connect = $connect;
    }

    public function beginTransaction() {
        $this->connect->beginTransaction();
    }

    public function commit() {
        $this->connect->commit();
    }

    function rollback() {
        $this->connect->rollback();
    }

    public function prepare($sql) {
        return $this->connect->prepare($sql);
    }

    function query($sql) {
        return $this->connect->query($sql);
    }

    function lastInsertId() {
        return $this->connect->lastInsertId();
    }

}
