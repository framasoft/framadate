<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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
    private $drivername = null;

    function __construct($connection_string, $user, $password) {
        $this->pdo = new \PDO($connection_string, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
	$this->drivername = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
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
	$result = null;
	switch($this->drivername()) {
		case "mysql":
		        $result = $this->query('SHOW TABLES');
			break;
		case "pgsql":
			$result = $this->query('SELECT tablename FROM pg_tables WHERE tablename !~ \'^pg_\' AND tablename !~ \'^sql_\';');
			break;
		default:
			return array();
	}
	
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

    function driverName() {
	return $this->drivername;
    }

    public function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
