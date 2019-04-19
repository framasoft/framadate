<?php
namespace Framadate\Repositories;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use PDOStatement;

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
     * @throws ConnectionException
     */
    public function commit()
    {
        $this->connect->commit();
    }

    /**
     * @throws ConnectionException
     */
    public function rollback()
    {
        $this->connect->rollback();
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->connect->createQueryBuilder();
    }

    /**
     * @param string $sql
     *@throws DBALException
     * @return bool|Statement|PDOStatement
     */
    public function prepare($sql)
    {
        return $this->connect->prepare($sql);
    }

    /**
     * @param string $sql
     *@throws DBALException
     * @return bool|Statement|PDOStatement
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
