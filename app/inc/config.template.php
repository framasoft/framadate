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

// Fully qualified domain name of your webserver.
// If this is unset or empty, the servername is determined automatically.
// You *have to set this* if you are running Framadate behind a reverse proxy.
// const APP_URL = '<www.mydomain.fr>';

// Application name
const NOMAPPLICATION = '<Application name>';

// Database administrator email
const ADRESSEMAILADMIN = '<email address>';

// Email for automatic responses (you should set it to "no-reply")
const ADRESSEMAILREPONSEAUTO = '<no-reply@mydomain.com>';

// Database user
const DB_USER= '<database user>';

// Database password
const DB_PASSWORD = '<database password>';

// Database server name, leave empty to use a socket
const DB_CONNECTION_STRING = 'mysql:host=<database host>;dbname=<database name>;port=<database port>';

// Name of the table that stores migration script already executed
const MIGRATION_TABLE = 'framadate_migration';

// Table name prefix
const TABLENAME_PREFIX = 'fd_';

// Default Language
const DEFAULT_LANGUAGE = 'fr';

// List of supported languages, fake constant as arrays can be used as constants only in PHP >=5.6
$ALLOWED_LANGUAGES = [
    'fr' => 'Français',
    'en' => 'English',
    'oc' => 'Occitan',
    'es' => 'Español',
    'de' => 'Deutsch',
    'nl' => 'Dutch',
    'it' => 'Italiano',
    'br' => 'Brezhoneg',
];

// Path to image file with the title
const IMAGE_TITRE = 'images/logo-framadate.png';

// Relative path to favicon
const FAVICON = 'favicon.ico';

// Clean URLs, boolean
const URL_PROPRE = false;

// Use REMOTE_USER data provided by web server
const USE_REMOTE_USER =  true;

// Path to the log file
const LOG_FILE = 'admin/stdout.log';

// Days (after expiration date) before purging a poll
const PURGE_DELAY = 60;

// Max slots per poll
const MAX_SLOTS_PER_POLL = 366;

// Number of seconds before we allow to resend an "Remember Edit Link" email.
const TIME_EDIT_LINK_EMAIL = 60;

// Config
$config = [
    /* general config */
    'use_smtp' => true,                     // use email for polls creation/modification/responses notification
    'smtp_options' => [
        'host' => 'localhost',              // SMTP server (you could add many servers (main and backup for example) : use ";" like separator
        'auth' => false,                    // Enable SMTP authentication
        'username' => '',                   // SMTP username
        'password' => '',                   // SMTP password
        'secure' => '',                     // Enable encryption (false, tls or ssl)
        'port' => 25,                       // TCP port to connect to
    ],
    'tracking_code' => '',                  // add HTML code to every page, useful for tools like Piwik
    /* home */
    'show_what_is_that' => true,            // display "how to use" section
    'show_the_software' => true,            // display technical information about the software
    'show_cultivate_your_garden' => true,   // display "development and administration" information
    /* create_classic_poll.php / create_date_poll.php */
    'default_poll_duration' => 180,         // default values for the new poll duration (number of days).
    /* create_classic_poll.php */
    'user_can_add_img_or_link' => true,     // user can add link or URL when creating his poll.
    'markdown_editor_by_default' => true,   // The markdown editor for the description is enabled by default
    'provide_fork_awesome' => true,         // Whether the build-in fork-awesome should be provided
];
