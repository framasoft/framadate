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
use function __f;
use Exception;
use Framadate\Utils;
use PDO;
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
        'dbConnectionString' => 'mysql:host=<HOST>;dbname=<SCHEMA>;port=3306',
        'dbUser' => 'root',
        'dbPassword' => '',
        'dbPrefix' => 'fd_',
        'migrationTable' => 'framadate_migration'
    ];

    public function __construct() {}

    public function updateFields($data): void
    {
        foreach ($data as $field => $value) {
            $this->fields[$field] = $value;
        }
    }

    public function install(Smarty &$smarty): array
    {
        // Check values are present
        if (empty($this->fields['appName']) || empty($this->fields['appMail']) || empty($this->fields['defaultLanguage']) || empty($this->fields['dbConnectionString']) || empty($this->fields['dbUser'])) {
            return $this->error('MISSING_VALUES');
        }

        // Connect to database
        try {
            $connect = $this->connectTo($this->fields['dbConnectionString'], $this->fields['dbUser'], $this->fields['dbPassword']);
        } catch(Exception $e) {
            return $this->error('CANT_CONNECT_TO_DATABASE', $e->getMessage());
        }

        // Write configuration to conf.php file
        if ($this->writeConfiguration($smarty) === false) {
            return $this->error(__f('Error', "Can't create the config.php file in '%s'.", CONF_FILENAME));
        }

        return $this->ok();
    }

    /**
     * Connect to PDO compatible source
     *
     * @param string $connectionString
     * @param string $user
     * @param string $password
     * @return PDO
     */
    public function connectTo(string $connectionString, string $user, string $password): PDO
    {
        $pdo = @new PDO($connectionString, $user, $password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    /**
     * @return false|int
     */
    public function writeConfiguration(Smarty &$smarty) {
        foreach($this->fields as $field=>$value) {
            $smarty->assign($field, $value);
        }

        $content = $smarty->fetch('admin/config.tpl');

        return $this->writeToFile($content);
    }

    /**
     * @param $content
     * @return false|int
     */
    public function writeToFile(string $content) {
        return @file_put_contents(CONF_FILENAME, $content);
    }

    /**
     * @return array
     */
    public function ok(): array
    {
        return [
            'status' => 'OK',
            'msg' => __f('Installation', 'Ended', Utils::get_server_name())
        ];
    }

    /**
     * @param string $msg
     * @param string $details
     * @return array
     */
    public function error(string $msg, string $details = ''): array
    {
        return [
            'status' => 'ERROR',
            'code' => $msg,
            'details' => $details,
        ];
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
