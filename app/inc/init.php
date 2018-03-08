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

use Doctrine\Common\Annotations\AnnotationRegistry;
use Framadate\Constraint\UniquePollConstraintValidator;
use Framadate\Controller\AdminPollController;
use Framadate\Controller\ClassicPollController;
use Framadate\Controller\CommentController;
use Framadate\Controller\DatePollController;
use Framadate\Controller\PollController;
use Framadate\Controller\VoteController;
use Framadate\Form\PollType;
use Framadate\FramaDB;
use Framadate\I18nWrapper;
use Framadate\Repositories\CommentRepository;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Services\AdminPollService;
use Framadate\Services\CommentService;
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\MailService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Services\SecurityService;
use Framadate\Utils;
use Silex\Provider\FormServiceProvider;

// Autoloading of dependencies with Composer
require_once __DIR__ . '/../../vendor/autoload.php';
AnnotationRegistry::registerLoader('class_exists');

/*if (ini_get('date.timezone') === '') {
    date_default_timezone_set('Europe/Paris');
}*/

define('ROOT_DIR', __DIR__ . '/../../');
define('CONF_FILENAME', ROOT_DIR . '/app/inc/config.php');

require_once __DIR__ . '/constants.php';

if (is_file(CONF_FILENAME)) {
    @include_once __DIR__ . '/config.php';

    // Connection to database
    $connect = new FramaDB(DB_CONNECTION_STRING, DB_USER, DB_PASSWORD);
    RepositoryFactory::init($connect);
    $err = 0;
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

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../../tpl',
    'twig.form.templates' => ['bootstrap_3_layout.html.twig']
]);

$app['config'] = $config;

$app['i18n'] = function () {
    return new I18nWrapper();
};

$app->extend('twig', function(Twig_Environment $twig, $app) {
    $trans = new Twig_SimpleFunction('__', function ($section, $key, $args = []) use ($app) {
        /** @var I18nWrapper $i18n */
        $i18n = $app['i18n'];
        return $i18n->get($section, $key, $args);
    });

    $route = new Twig_SimpleFunction('poll_url', function ($id, $admin = false, $action = false, $action_value = false, $vote_id = '') {
        $poll_id =  filter_var($id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
        $action = $action === false ?: Utils::htmlEscape($action);
        $action_value = $action_value === false ?: $action_value;
        $vote_unique_id = $vote_id === '' ?: filter_var($vote_id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);

        // If filter_var fails (i.e.: hack tentative), it will return false. At least no leak is possible from this.

        return Utils::getUrlSondage($poll_id, $admin, $vote_unique_id, $action, $action_value);
    });

    $preg_match = new Twig_SimpleFunction('preg_match', function($pattern, $subject) {
        return preg_match($pattern, $subject);
    });

    $addslashes = new Twig_SimpleFilter('addslashes_single_quote', function ($string) {
        return addcslashes($string, '\\\'');
    });

    $markdown = new Twig_SimpleFilter('markdown', function ($string) {
        $parsedown = new Parsedown();
        return $parsedown->text($string);
    });

    $twig->addFunction($trans);
    $twig->addFunction($route);
    $twig->addFunction($preg_match);
    $twig->addFilter($addslashes);
    $twig->addFilter($markdown);

    $twig->addExtension(new Twig_Extension_Debug());
    $twig->addExtension(new Twig_Extensions_Extension_Intl());

    $twig->addGlobal('APPLICATION_NAME', NOMAPPLICATION);
    // $twig->addGlobal('SERVER_URL', Utils::get_server_name());
    $twig->addGlobal('SCRIPT_NAME', $_SERVER['SCRIPT_NAME']);
    $twig->addGlobal('TITLE_IMAGE', IMAGE_TITRE);
    $twig->addGlobal('use_nav_js', false); // strstr($_SERVER['SERVER_NAME'], 'framadate.org'));
    $twig->addGlobal('locale', $app['i18n']->getLocale());
    $twig->addGlobal('langs', ALLOWED_LANGUAGES);
    // $twig->addGlobal('date_format', date_format);
    if (isset($app['config']['tracking_code'])) {
        $twig->addGlobal('tracking_code', $app['config']['tracking_code']);
    }
    if (defined('FAVICON')) {
        $twig->addGlobal('favicon', FAVICON);
    }
    return $twig;

});

$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
));

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new FormServiceProvider());

$app->extend('form.types', function ($types) use ($app) {
    $types[] = new PollType();

    return $types;
});

$app->register(new Silex\Provider\LocaleServiceProvider());

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));

$app->register(new Silex\Provider\ValidatorServiceProvider());

$app['debug'] = true;

$app->register(new Silex\Provider\HttpFragmentServiceProvider());

$app->register(new Silex\Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../../cache/profiler',
    'profiler.mount_prefix' => '/_profiler', // this is the default
));

$app['log.service'] = function () {
    return new LogService();
};

$app['poll.service'] = function () use ($app, $connect) {
    return new PollService($connect, $app['log.service']);
};

$app['admin_poll.service'] = function () use ($app, $connect) {
    return new AdminPollService($connect, $app['poll.service'], $app['log.service']);
};

$app['validator.unique_poll'] = function () use ($app) {
    $validator = new UniquePollConstraintValidator();
    $validator->setPollService($app['poll.service']);

    return $validator;
};

$app['mail.service'] = function () use ($app) {
    return new MailService($app['session'], $app['config']['use_smtp']);
};

$app['notification.service'] = function () use ($app) {
    return new NotificationService($app['mail.service'], $app['session'], $app['i18n']);
};

$app['input.service'] = function() {
    return new InputService();
};

$app['security.service'] = function () use ($app) {
    return new SecurityService($app['session']);
};

$app['purge.service'] = function () use ($connect, $app) {
    return new PurgeService($connect, $app['log.service']);
};

$app['comment.repository'] = function () use ($connect) {
    return new CommentRepository($connect);
};

$app['comment.service'] = function () use ($app) {
    return new CommentService($app['comment.repository'], $app['log.service']);
};

$app['poll.controller'] = function() use ($app) {
    return new PollController($app['poll.service'], $app['url_generator'], $app['security.service'], $app['twig'], $app['i18n'], $app['session'], $app['form.factory'], $app['config']);
};

$app['vote.controller'] = function() use ($app) {
    return new VoteController($app['poll.service'], $app['url_generator'], $app['twig'], $app['i18n'], $app['session'], $app['input.service'], $app['notification.service'], $app['config']);
};

$app['date_poll.controller'] = function() use ($app) {
    return new DatePollController($app['poll.service'], $app['url_generator'], $app['mail.service'], $app['purge.service'], $app['twig'], $app['i18n'], $app['session'], $app['form.factory'], $app['config']);
};

$app['classic_poll.controller'] = function() use ($app) {
    return new ClassicPollController($app['poll.service'], $app['url_generator'], $app['mail.service'], $app['purge.service'], $app['twig'], $app['i18n'], $app['session'], $app['form.factory'], $app['config']);
};

$app['poll_admin.controller'] = function() use ($app) {
    return new AdminPollController($app['poll.service'], $app['input.service'], $app['admin_poll.service'], $app['notification.service'], $app['url_generator'], $app['session'], $app['twig'], $app['i18n'], $app['config']);
};

$app['comment.controller'] = function () use ($app) {
    return new CommentController(
        $app['poll.service'],
        $app['comment.service'],
        $app['security.service'],
        $app['mail.service'],
        $app['input.service'],
        $app['notification.service'],
        $app['url_generator'],
        $app['session'],
        $app['i18n'],
        $app['twig'],
        $app['config']
    );
};
