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

use Framadate\Message;
use Framadate\Utils;

define('ROOT_DIR', __DIR__ . '/../');

/**
 * Checking for missing vendors.
 */
if (!file_exists(ROOT_DIR . 'vendor/autoload.php')) {
    die ("ERROR: You should use <code>composer install</code> to fetch dependant libraries.");
}

/**
 * Stripped ini sequence
 */
require_once ROOT_DIR . 'vendor/autoload.php';
require_once ROOT_DIR . 'app/inc/constants.php';
if (session_id() === '') {
    session_start();
}
$ALLOWED_LANGUAGES = [
    'fr' => 'Français',
    'en' => 'English',
    'oc' => 'Occitan',
    'es' => 'Español',
    'de' => 'Deutsch',
    'it' => 'Italiano',
    'br' => 'Brezhoneg',
];
const DEFAULT_LANGUAGE = 'en';
require_once ROOT_DIR . 'app/inc/i18n.php';

/**
 * Function to sort messages by type (priorise errors on warning, warning on info, etc.)
 *
 * @param Message $a
 * @param Message $b
 * @return int
 */
function compareCheckMessage(Message $a, Message $b)
{
    $values = [
        'danger' => 0,
        'warning' => 1,
        'info' => 2,
        'success' => 3
    ];
    $vA = $values[$a->type];
    $vB = $values[$b->type];

    if ($vA === $vB) {
        return 0;
    }
    return ($vA < $vB) ? -1 : 1;
}

/**
 * Vars
 */
$messages = [];
$inc_directory = ROOT_DIR . 'app/inc/';
$conf_filename = $inc_directory . 'config.php';

/**
 * Messages
 */

// PHP Version
if (version_compare(PHP_VERSION, PHP_NEEDED_VERSION) >= 0) {
    $messages[] = new Message('info', __f('Check','PHP version %s is enough (needed at least PHP %s).', PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION, PHP_NEEDED_VERSION));
} else {
    $messages[] = new Message('danger', __f('Check','Your PHP version (%s) is too old. This application needs at least PHP %s.', phpversion(), PHP_NEEDED_VERSION));
}

// INTL extension
if (extension_loaded('intl')) {
    $messages[] = new Message('info', __('Check','PHP Intl extension is enabled.'));
} else {
    $messages[] = new Message('danger', __('Check','You need to enable the PHP Intl extension.'));
}

// Is template compile dir exists and writable ?
if (!file_exists(ROOT_DIR . COMPILE_DIR)) {
    $messages[] = new Message('danger', __f('Check','The template compile directory (%s) doesn\'t exist in "%s". Retry the installation process.', COMPILE_DIR, realpath(ROOT_DIR)));
} elseif (is_writable(ROOT_DIR . COMPILE_DIR)) {
    $messages[] = new Message('info', __f('Check','The template compile directory (%s) is writable.', realpath(ROOT_DIR . COMPILE_DIR)));
} else {
    $messages[] = new Message('danger', __f('Check','The template compile directory (%s) is not writable.', realpath(ROOT_DIR . COMPILE_DIR)));
}

// Does config.php exists or is writable ?
if (file_exists($conf_filename)) {
    $messages[] = new Message('info', __('Check','The config file exists.'));
} elseif (is_writable($inc_directory)) {
    $messages[] = new Message('info', __f('Check','The config file directory (%s) is writable.', $inc_directory));
} else {
    $messages[] = new Message('danger', __f('Check','The config file directory (%s) is not writable and the config file (%s) does not exists.', $inc_directory, $conf_filename));
}

// Security
if (extension_loaded('openssl')) {
    $messages[] = new Message('info', __('Check','OpenSSL extension loaded.'));
} else {
    $messages[] = new Message('warning', __('Check','Consider enabling the PHP extension OpenSSL for increased security.'));
}

if (ini_get('session.cookie_httponly') === '1') {
    $messages[] = new Message('info', __('Check', 'Cookies are served from HTTP only.'));
} else {
    $messages[] = new Message('warning', __('Check', "Consider setting « session.cookie_httponly = 1 » inside your php.ini or add « php_value session.cookie_httponly 1 » to your .htaccess so that cookies can't be accessed through Javascript."));
}

// Datetime
$timezone = ini_get('date.timezone');
if (!empty($timezone)) {
    $messages[] = new Message('info', __('Check','date.timezone is set.'));
} else {
    $messages[] = new Message('warning', __('Check','Consider setting the date.timezone in php.ini.'));
}

// The percentage of steps needed to be ready to launch the application
$errors = 0;
$warnings = 0;
foreach ($messages as $message) {
    if ($message->type === 'danger') {
        $errors++;
    } else if ($message->type === 'warning') {
        $warnings++;
    }
}
$readyPercentage = round((count($messages)-$errors)*100/count($messages));

if ($errors > 0) {
    $readyClass = 'danger';
} else if ($warnings > 0) {
    $readyClass = 'warning';
} else {
    $readyClass = 'success';
}

usort($messages, 'compareCheckMessage');

?>
<!DOCTYPE html>
<html lang="<?=$locale?>">
<head>
    <meta charset="utf-8">

    <title><?=__('Check', 'Installation checking') ?></title>

    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/frama.css">
</head>
<body>
    <div class="container ombre">
        <div class="row">
            <form method="get" action="" class="hidden-print">
                <div class="input-group input-group-sm pull-right col-xs-12 col-sm-2">
                    <select name="lang" class="form-control" title="<?=__('Language selector', 'Select language')?>" >
                        <?php foreach ($ALLOWED_LANGUAGES as $lang_key => $language) { ?>
                        <option lang="fr" <?php if (substr($lang_key, 0, 2)===$locale) { echo 'selected';} ?> value="<?=substr($lang_key, 0, 2)?>"><?=$language?></option>
                        <?php } ?>
                    </select>
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-default btn-sm" title="<?=__('Language selector', 'Select language')?>">OK</button>
                    </span>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h1><?=__('Check', 'Installation checking') ?></h1>
                <div>
                    <div class="progress">
                        <div class="progress-bar  progress-bar-<?= $readyClass ?>" role="progressbar" aria-valuenow="<?= $readyPercentage ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $readyPercentage ?>%;">
                            <?= $readyPercentage ?>%
                        </div>
                    </div>
                </div>
                <div>
                    <?php
                        foreach ($messages as $message) {
                            echo '<div class="alert alert-' . $message->type . '" role="alert">';
                            echo Utils::htmlEscape($message->message);
                            echo '<span class="sr-only">' . $message->type . '</span>';
                            echo '</div>';
                         }
                    ?>
                </div>
            </div>
            <div class="text-center">
                <a class="btn btn-info" role="button" href=""><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> <?= __('Check', 'Check again') ?></a>
                <?php
                if (!is_file($conf_filename)) {
                    if ($errors === 0) {
                ?>
                    <a class="btn btn-primary" role="button" href="<?= Utils::get_server_name() . 'admin/install.php' ?>"><span class=" glyphicon glyphicon-arrow-right" aria-hidden="true"></span> <?= __('Check', 'Continue the installation') ?></a>
                <?php
                    }
                } else {
                ?>
                    <a class="btn btn-primary" role="button" href="<?= Utils::get_server_name() . 'admin/'?>"><span class=" glyphicon glyphicon-arrow-left" aria-hidden="true"></span> <?= __('Admin', 'Back to administration') ?></a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</body>
