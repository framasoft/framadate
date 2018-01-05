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
use Smarty;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InstallService {

    private $fields = array(
        // General
        'appName' => 'Framadate',
        'appMail' => '',
        'responseMail' => '',
        'defaultLanguage' => 'fr',
        'cleanUrl' => true,

        // Database configuration
        'dbConnectionString' => '',
        'dbUser' => 'root',
        'dbPassword' => '',
        'dbPrefix' => 'fd_',
        'migrationTable' => 'framadate_migration',
        'base' => 'mysql',
         'dataname' => '',
        'server' => 'localhost',
        'nameadmin'=> 'admin',
        'passadmin'=> 'elio13790'     
    );

private $admin;

private $pass;

private $htaccess;
 
private $htpasswd;  

private $file;

function __construct() {}

function dbconnect(){

return $dbconnect = $this->fields['base'].":host=".$this->fields['server'].";dbname=".$this->fields['dataname'];

}

public function admin($admin,$Pass){

$fileadmin =realpath("./")."/.htaccess";

$file = fopen($fileadmin,"w+");

$write = $this->Htaccess();

fwrite($file,$write);

fclose($file);

$filepass = realpath("./")."/.htpasswd";

$pass = fopen($filepass, "w+");

$write = $this->Htpasswd($admin,$Pass);

fwrite($pass,$write);

fclose($pass);

}

public function Htaccess(){

$text = "AuthName";          

$t = '"';

$text =  $text." ".$t."Page d'administration protégée".$t."\n" ;

$text = $text."AuthType Basic \n";

$text  = $text."AuthUserFile ".'"'.realpath("./").'/.htpasswd'.'"'."\n";

$text = $text."Require valid-user";

$htacess =$text;

return $htacess;
}

public function Htpasswd($admin,$pass){

$htpasswd = $this->fields['nameadmin'].":".$this->Passadmin($pass);

return $htpasswd;

}

public function Nameadmin(){

$admin = $this->fields['nameadmin'];

return $admin;

}

public function Passadmin($Pass){

return $pass = crypt($Pass);

}

    public function updateFields($data) {
        foreach ($data as $field => $value) {
            $this->fields[$field] = $value;
        }
    }

    public function install(Smarty &$smarty) {
        // Check values are present
        if (empty($this->fields['appName']) || empty($this->fields['appMail']) || empty($this->fields['defaultLanguage']) || empty($this->fields['dataname']) || empty($this->fields['dbUser'])  || empty($this->fields['server'])) {
            return $this->error('MISSING_VALUES');
        }

 $this->fields['dbConnectionString'] = $this->dbconnect();


        // Connect to database
        $connect = $this->connectTo($this->fields['dbConnectionString'], $this->fields['dbUser'], $this->fields['dbPassword']);
        if (!$connect) {
            return $this->error('CANT_CONNECT_TO_DATABASE');
        }

        // Write configuration to conf.php file
        if ($this->writeConfiguration($smarty) === false) {
            return $this->error(__f('Error', "Can't create the config.php file in '%s'.", CONF_FILENAME));
        }



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

    function writeConfiguration(Smarty &$smarty) {
        foreach($this->fields as $field=>$value) {
            $smarty->assign($field, $value);
        }

        $content = $smarty->fetch('admin/config.tpl');

        return $this->writeToFile($content);
    }
  
    /**
     * @param $content
     */
    function writeToFile($content) {
        return @file_put_contents(CONF_FILENAME, $content);
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
