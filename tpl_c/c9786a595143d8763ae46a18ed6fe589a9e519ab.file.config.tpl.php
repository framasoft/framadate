<?php /* Smarty version Smarty-3.1.21, created on 2017-09-27 17:17:28
         compiled from "/var/www/framadate//tpl/admin/config.tpl" */ ?>
<?php /*%%SmartyHeaderCode:150495899159cbc108915582-86246882%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9786a595143d8763ae46a18ed6fe589a9e519ab' => 
    array (
      0 => '/var/www/framadate//tpl/admin/config.tpl',
      1 => 1506522320,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '150495899159cbc108915582-86246882',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'appName' => 0,
    'appMail' => 0,
    'responseMail' => 0,
    'dbConnectionString' => 0,
    'dbUser' => 0,
    'dbPassword' => 0,
    'dbPrefix' => 0,
    'migrationTable' => 0,
    'defaultLanguage' => 0,
    'cleanUrl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21',
  'unifunc' => 'content_59cbc108935339_51896374',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59cbc108935339_51896374')) {function content_59cbc108935339_51896374($_smarty_tpl) {?><?php echo '<?php'; ?>

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
const NOMAPPLICATION = '<?php echo smarty_modifier_addslashes_single_quote($_smarty_tpl->tpl_vars['appName']->value);?>
';

// Database administrator email
const ADRESSEMAILADMIN = '<?php echo $_smarty_tpl->tpl_vars['appMail']->value;?>
';

// Email for automatic responses (you should set it to "no-reply")
const ADRESSEMAILREPONSEAUTO = '<?php echo $_smarty_tpl->tpl_vars['responseMail']->value;?>
';

// Database server name, leave empty to use a socket
const DB_CONNECTION_STRING = '<?php echo $_smarty_tpl->tpl_vars['dbConnectionString']->value;?>
';

// Database user
const DB_USER= '<?php echo $_smarty_tpl->tpl_vars['dbUser']->value;?>
';

// Database password
const DB_PASSWORD = '<?php echo smarty_modifier_addslashes_single_quote($_smarty_tpl->tpl_vars['dbPassword']->value);?>
';

// Table name prefix
const TABLENAME_PREFIX = '<?php echo $_smarty_tpl->tpl_vars['dbPrefix']->value;?>
';

// Name of the table that stores migration script already executed
const MIGRATION_TABLE = '<?php echo $_smarty_tpl->tpl_vars['migrationTable']->value;?>
';

// Default Language
const DEFAULT_LANGUAGE = '<?php echo $_smarty_tpl->tpl_vars['defaultLanguage']->value;?>
';

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

// Clean URLs, boolean
const URL_PROPRE = <?php if (in_array($_smarty_tpl->tpl_vars['cleanUrl']->value,array('1','on','true'))) {?>true<?php } else { ?>false<?php }?>;

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
    /* home */
    'show_what_is_that' => true,            // display "how to use" section
    'show_the_software' => true,            // display technical information about the software
    'show_cultivate_your_garden' => true,   // display "development and administration" information
    /* create_classic_poll.php / create_date_poll.php */
    'default_poll_duration' => 180,         // default values for the new poll duration (number of days).
    /* create_classic_poll.php */
    'user_can_add_img_or_link' => true,     // user can add link or URL when creating his poll.
];
<?php }} ?>
