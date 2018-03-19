<?php
namespace Framadate\Repository;

use Doctrine\DBAL\Connection;

abstract class AbstractRepository
{
    /**
     * @var Connection
     */
    protected $connect;

    /**
     * PollRepository constructor.
     * @param Connection $connect
     */
    public function __construct(Connection $connect)
    {
        $this->connect = $connect;
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
     * @param $sql
     * @return \Doctrine\DBAL\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function prepare($sql)
    {
        return $this->connect->prepare($sql);
    }

    /**
     * @param $sql
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
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
