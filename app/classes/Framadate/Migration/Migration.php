<?php
namespace Framadate\Migration;

interface Migration {

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description();

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true if the Migration should be executed
     */
    function preCondition(\PDO $pdo);

    /**
     * This methode is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true if the execution succeeded
     */
    function execute(\PDO $pdo);

}
 