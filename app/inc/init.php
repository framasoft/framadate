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

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Services\LogService;

// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_id() === '') {
    session_start();
}

if (ini_get('date.timezone') === '') {
    date_default_timezone_set('Europe/Paris');
}

define('ROOT_DIR', __DIR__ . '/../../');

$path = '/app/inc/config.php';
if (getenv('APP_ENV') === 'test') {
    $path = '/app/inc/config.test.php';
    // Read DB connection params from Environment Variables if possible
    define('FRAMADATE_DB_DRIVER', getenv('FRAMADATE_DB_DRIVER') ? getenv('FRAMADATE_DB_DRIVER') : 'pdo_sqlite');
    define('FRAMADATE_DB_NAME', getenv('FRAMADATE_DB_NAME') ? getenv('FRAMADATE_DB_NAME') : 'framadate');
    define('FRAMADATE_DB_HOST', getenv('FRAMADATE_DB_HOST') ? getenv('FRAMADATE_DB_HOST') : '');
    define('FRAMADATE_DB_PORT', getenv('FRAMADATE_DB_PORT') ? getenv('FRAMADATE_DB_PORT') : '');
    define('FRAMADATE_DB_USER', getenv('FRAMADATE_DB_USER') ? getenv('FRAMADATE_DB_USER') : '');
    define('FRAMADATE_DB_PASSWORD', getenv('FRAMADATE_DB_PASSWORD') ? getenv('FRAMADATE_DB_PASSWORD') : '');
}
define('CONF_FILENAME', ROOT_DIR . $path);

require_once __DIR__ . '/constants.php';

if (is_file(CONF_FILENAME)) {
    @include_once CONF_FILENAME;

    // Connection to database
    $doctrineConfig = new Configuration();
    $connectionParams = [
        'dbname' => DB_NAME,
        'user' => DB_USER,
        'password' => DB_PASSWORD,
        'host' => DB_HOST,
        'driver' => DB_DRIVER,
        'charset' => DB_DRIVER === 'pdo_mysql' ? 'utf8mb4' : 'utf8',
    ];

    if (DB_DRIVER === 'pdo_sqlite') {
        $connectionParams['path'] = ROOT_DIR . '/test_database.sqlite';
    }

    try {
        $connect = DriverManager::getConnection($connectionParams, $doctrineConfig);
        RepositoryFactory::init($connect);
        $err = 0;
    } catch (DBALException $e) {
        $logger = new LogService();
        $logger->log('ERROR', $e->getMessage());
    }
} else {
    define('NOMAPPLICATION', 'Framadate');
    define('DEFAULT_LANGUAGE', 'fr');
    define('IMAGE_TITRE', 'images/logo-framadate.png');
    define('LOG_FILE', 'admin/stdout.log');
    $ALLOWED_LANGUAGES = [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'br' => 'Brezhoneg',
    ];
}

require_once __DIR__ . '/i18n.php';
// Smarty
require_once __DIR__ . '/smarty.php';

require_once __DIR__ . '/services.php';
Services::init($connect, $smarty);
