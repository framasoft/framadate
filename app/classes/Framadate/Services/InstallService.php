<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
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
namespace Framadate\Services;
use Framadate\Utils;
use Smarty;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InstallService {

    private $fields = array(
        'General' =>
            array(
                'appName' => 'Framadate',
                'appMail' => '',
                'responseMail' => '',
                'defaultLanguage' => 'fr',
                'cleanUrl' => true
            ),
        'Database configuration' =>
            array(
                'dbConnectionString' => 'mysql:host=<HOST>;dbname=<SCHEMA>;port=3306',
                'dbUser' => 'root',
                'dbPassword' => '',
                'dbPrefix' => 'fd_',
                'migrationTable' => 'framadate_migration'
            )
    );

    function __construct() {}

    public function install($data, Smarty &$smarty) {
        // Check values are present
        if (empty($data['appName']) || empty($data['appMail']) || empty($data['defaultLanguage']) || empty($data['dbConnectionString']) || empty($data['dbUser'])) {
            return $this->error('MISSING_VALUES');
        }

        // Connect to database
        $connect = $this->connectTo($data['dbConnectionString'], $data['dbUser'], $data['dbPassword']);
        if (!$connect) {
            return $this->error('CANT_CONNECT_TO_DATABASE');
        }

        // Create database schema
        $this->createDatabaseSchema($connect);

        // Write configuration to conf.php file
        $this->writeConfiguration($data, $smarty);

        return $this->ok();
    }

    function connectTo($connectionString, $user, $password) {
        try {
            $pdo = @new \PDO($connectionString, $user, $password);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch(\Exception $e) {
            return null;
        }
    }

    function writeConfiguration($data, Smarty &$smarty) {
        foreach($this->fields as $groupKey=>$group) {
            foreach ($group as $field=>$value) {
                $smarty->assign($field, $data[$field]);
            }
        }

        $content = $smarty->fetch('admin/config.tpl');

        $this->writeToFile($content);
    }

    /**
     * @param $content
     */
    function writeToFile($content) {
        file_put_contents(CONF_FILENAME, $content);
    }

    /**
     * Execute SQL installation scripts.
     *
     * @param \PDO $connect
     */
    function createDatabaseSchema($connect) {
        $dir = opendir(ROOT_DIR . '/install/');
        while ($dir && ($file = readdir($dir)) !== false) {
            if ($file !== '.' && $file !== '..' && strpos($file, '.mysql.auto.sql')) {
                $statement = file_get_contents(ROOT_DIR . '/install/' . $file);
                $connect->exec($statement);
            }
        }
    }

    /**
     * @return array
     */
    function ok() {
        return array(
            'status' => 'OK',
            'msg' => __f('Installation', 'Ended', Utils::get_server_name())
        );
    }

    /**
     * @param $msg
     * @return array
     */
    function error($msg) {
        return array(
            'status' => 'ERROR',
            'code' => $msg
        );
    }

    public function getFields() {
        return $this->fields;
    }

}
