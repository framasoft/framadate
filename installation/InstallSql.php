<?php

class InstallSql
{
    public function inject()
    {
        require_once __DIR__.'/../app/inc/init.php';

        if ($connect->ErrorMsg() !== '') {
            throw new \Exception('Bad database configuration : '.$connect->ErrorMsg());
        }

        $sqls = explode("\n", file_get_contents(__DIR__.'/install.mysql.auto.sql'));
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if (empty($sql) === true) {
                continue;
            }

            $query = $connect->Prepare($sql);
            $cleaning = $connect->Execute($query);
        }
    }
}
