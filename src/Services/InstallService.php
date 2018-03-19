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
 * Ce logiciel est r�gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Services;

use Framadate\Utils;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InstallService
{
    private $fields = [
        // General
        'appName' => 'Framadate',
        'appMail' => '',
        'responseMail' => '',
        'defaultLanguage' => 'fr',
        'cleanUrl' => true,

        // Database configuration
        'dbConnectionString' => 'mysql:host=<HOST>;dbname=<SCHEMA>;port=3306',
        'dbUser' => 'root',
        'dbPassword' => '',
        'dbPrefix' => 'fd_',
        'migrationTable' => 'framadate_migration'
    ];

    public function __construct()
    {
    }

    public function updateFields($data)
    {
        foreach ($data as $field => $value) {
            $this->fields[$field] = $value;
        }
    }

    public function install(\Twig_Environment &$twig)
    {
        // Check values are present
        if (empty($this->fields['appName']) || empty($this->fields['appMail']) || empty($this->fields['defaultLanguage']) || empty($this->fields['dbConnectionString']) || empty($this->fields['dbUser'])) {
            return $this->error('MISSING_VALUES');
        }

        // Connect to database
        $connect = $this->connectTo($this->fields['dbConnectionString'], $this->fields['dbUser'], $this->fields['dbPassword']);
        if (!$connect) {
            return $this->error('CANT_CONNECT_TO_DATABASE');
        }

        // Write configuration to conf.php file
        if ($this->writeConfiguration($twig) === false) {
            return $this->error(__f('Error', "Can't create the config.php file in '%s'.", CONF_FILENAME));
        }

        return $this->ok();
    }

    public function connectTo($connectionString, $user, $password)
    {
        try {
            $pdo = @new \PDO($connectionString, $user, $password);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function writeConfiguration(\Twig_Environment &$twig)
    {
        $content = $twig->render('admin/config.twig', $this->fields);

        return $this->writeToFile($content);
    }

    /**
     * @param $content
     */
    public function writeToFile($content)
    {
        return @file_put_contents(CONF_FILENAME, $content);
    }

    /**
     * @return array
     */
    public function ok()
    {
        return [
            'status' => 'OK',
            'msg' => __f('Installation', 'Ended', Utils::get_server_name())
        ];
    }

    /**
     * @param $msg
     * @return array
     */
    public function error($msg)
    {
        return [
            'status' => 'ERROR',
            'code' => $msg
        ];
    }

    public function getFields()
    {
        return $this->fields;
    }
}
