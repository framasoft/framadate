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
use Framadate\FramaDB;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Utils;

// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/o80/i18n/src/shortcuts.php';

if (session_id() === '') {
    session_start();
}

if (ini_get('date.timezone') === '') {
    date_default_timezone_set('Europe/Paris');
}

define('ROOT_DIR', __DIR__ . '/../../');
define('CONF_FILENAME', ROOT_DIR . '/app/inc/config.php');

require_once __DIR__ . '/constants.php';

if (is_file(CONF_FILENAME)) {
    @include_once __DIR__ . '/config.php';

    try {
        // Connection to database
        $connect = new FramaDB(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
        RepositoryFactory::init($connect);
    } catch (PDOException $e) {
        if ($_SERVER['SCRIPT_NAME'] !== '/maintenance.php') {
            header(('Location: ' . Utils::get_server_name() . 'maintenance.php'));
            exit;
        }
        $error = $e->getMessage();
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
        'ca' => 'Català'
    ];
}

require_once __DIR__ . '/i18n.php';
// Smarty
require_once __DIR__ . '/smarty.php';
