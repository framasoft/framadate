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
    public function __construct(FramaDB $connect) {
        $this->connect = $connect;
    }

    public function beginTransaction(): void
    {
        $this->connect->beginTransaction();
    }

    public function commit(): void
    {
        $this->connect->commit();
    }

    public function rollback(): void
    {
        $this->connect->rollback();
    }

    /**
     * @return \PDOStatement|false
     */
    public function prepare(string $sql) {
        return $this->connect->prepare($sql);
    }

    /**
     * @return \PDOStatement|false
     */
    public function query($sql) {
        return $this->connect->query($sql);
    }

    public function lastInsertId(): string {
        return $this->connect->lastInsertId();
    }
}
