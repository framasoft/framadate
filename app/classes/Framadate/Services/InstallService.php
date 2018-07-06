<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha�l DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Services;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Framadate\Utils;
use Smarty;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InstallService {
    private $fields = [
        // General
        'appName' => 'Framadate',
        'appMail' => '',
        'responseMail' => '',
        'defaultLanguage' => 'fr',
        'cleanUrl' => true,

        // Database configuration
        'dbName' => 'framadate',
        'dbPort' => 3306,
        'dbHost' => 'localhost',
        'dbUser' => 'root',
        'dbPassword' => '',
        'dbPrefix' => 'fd_',
        'migrationTable' => 'framadate_migration'
    ];

    function __construct() {}

    public function updateFields($data) {
        foreach ($data as $field => $value) {
            $this->fields[$field] = $value;
        }
    }

    public function install(Smarty &$smarty) {
        // Check values are present
        if (empty($this->fields['appName']) || empty($this->fields['appMail']) || empty($this->fields['defaultLanguage']) || empty($this->fields['dbName']) || empty($this->fields['dbHost']) || empty($this->fields['dbPort']) || empty($this->fields['dbUser'])) {
            return $this->error('Missing values');
        }

        // Connect to database
        try {
            $connect = $this->connectTo($this->fields);
        } catch(\Doctrine\DBAL\DBALException $e) {
            return $this->error('Unable to connect to database', $e->getMessage());
        }

        // Write configuration to conf.php file
        if ($this->writeConfiguration($smarty) === false) {
            return $this->error(__f('Error', "Can't create the config.php file in '%s'.", CONF_FILENAME));
        }

        return $this->ok();
    }

    /**
     * @param $fields
     * @return \Doctrine\DBAL\Connection|null
     */
    function connectTo($fields) {
        $doctrineConfig = new Configuration();
        $connectionParams = [
            'dbname' => $fields['dbName'],
            'user' => $fields['dbUser'],
            'password' => $fields['dbPassword'],
            'host' => $fields['dbHost'],
            'driver' => $fields['dbDriver'],
            'charset' => $fields['dbDriver'] === 'pdo_mysql' ? 'utf8mb4' : 'utf8',
        ];
        return DriverManager::getConnection($connectionParams, $doctrineConfig);
    }

    function writeConfiguration(Smarty &$smarty) {
        foreach($this->fields as $field=>$value) {
            $smarty->assign($field, $value);
        }

        $content = $smarty->fetch('admin/config.tpl');

        return $this->writeToFile($content);
    }

    /**
     * @param $content
     * @return bool|int
     */
    function writeToFile($content) {
        return @file_put_contents(CONF_FILENAME, $content);
    }

    /**
     * @return array
     */
    function ok() {
        return [
            'status' => 'OK',
            'msg' => __f('Installation', 'Ended', Utils::get_server_name())
        ];
    }

    /**
     * @param $msg
     * @return array
     */
    function error($msg, $details = '') {
        return [
            'status' => 'ERROR',
            'code' => $msg,
            'details' => $details,
        ];
    }

    public function getFields() {
        return $this->fields;
    }
}
