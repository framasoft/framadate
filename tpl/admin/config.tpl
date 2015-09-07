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

// Fully qualified domain name of your webserver.
// If this is unset or empty, the servername is determined automatically.
// You *have to set this* if you are running Framedate behind a reverse proxy.
// const APP_URL = '<www.mydomain.fr>';

// Application name
const NOMAPPLICATION = '{$appName}';

// Database administrator email
const ADRESSEMAILADMIN = '{$appMail}';

// Email for automatic responses (you should set it to "no-reply")
const ADRESSEMAILREPONSEAUTO = '{$responseMail}';

// Database server name, leave empty to use a socket
const DB_CONNECTION_STRING = '{$dbConnectionString}';

// Database user
const DB_USER= '{$dbUser}';

// Database password
const DB_PASSWORD = '{$dbPassword}';

// Table name prefix
const TABLENAME_PREFIX = '{$dbPrefix}';

// Name of the table that store migration script already executed
const MIGRATION_TABLE = '{$migrationTable}';

// Default Language
const DEFAULT_LANGUAGE = '{$defaultLanguage}';

// List of supported languages, fake constant as arrays can be used as constants only in PHP >=5.6
$ALLOWED_LANGUAGES = [
    'fr' => 'Français',
    'en' => 'English',
    'es' => 'Español',
    'de' => 'Deutsch',
    'it' => 'Italiano',
];

// Nom et emplacement du fichier image contenant le titre
const IMAGE_TITRE = 'images/logo-framadate.png';

// Clean URLs, boolean
const URL_PROPRE = {if in_array($cleanUrl, array('1', 'on', 'true'))}true{else}false{/if};

// Use REMOTE_USER data provided by web server
const USE_REMOTE_USER =  true;

// Path to the log file
const LOG_FILE = 'admin/stdout.log';

// Days (after expiration date) before purge a poll
const PURGE_DELAY = 60;

// Config
$config = [
    /* general config */
    'use_smtp' => true,                     // use email for polls creation/modification/responses notification
    /* home */
    'show_what_is_that' => true,            // display "how to use" section
    'show_the_software' => true,            // display technical information about the software
    'show_cultivate_your_garden' => true,   // display "developpement and administration" information
    /* create_classic_poll.php / create_date_poll.php */
    'default_poll_duration' => 180,         // default values for the new poll duration (number of days).
    /* create_classic_poll.php */
    'user_can_add_img_or_link' => true,     // user can add link or URL when creating his poll.
];

