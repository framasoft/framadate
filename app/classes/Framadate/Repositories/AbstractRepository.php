<?php
namespace Framadate\Repositories;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository {
    /**
     * @var Connection
     */
    protected $connect;

    /**
     * PollRepository constructor.
     * @param Connection $connect
     */
    public function __construct(Connection $connect) {
        $this->connect = $connect;
        $this->connect->setFetchMode(\PDO::FETCH_OBJ);
    }

    public function beginTransaction()
    {
        $this->connect->beginTransaction();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commit()
    {
        $this->connect->commit();
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollback()
    {
        $this->connect->rollback();
    }

    /**
     * @param string $sql
     * @throws \Doctrine\DBAL\DBALException
     * @return bool|\Doctrine\DBAL\Driver\Statement|\PDOStatement
     */
    public function prepare($sql)
    {
        return $this->connect->prepare($sql);
    }

    /**
     * @param string $sql
     * @throws \Doctrine\DBAL\DBALException
     * @return bool|\Doctrine\DBAL\Driver\Statement|\PDOStatement
     */
    public function query($sql)
    {
        return $this->connect->query($sql);
    }

    /**
     * @return string
     */
    public function lastInsertId()
    {
        return $this->connect->lastInsertId();
    }
}
