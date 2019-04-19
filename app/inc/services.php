<?php
use Doctrine\DBAL\Connection;
use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\LogService;
use Framadate\Services\MailService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Services\SecurityService;
use Framadate\Services\SessionService;

class Services {
    private static $connect;
    private static $smarty;

    private static $adminPollService;
    private static $inputService;
    private static $logService;
    private static $mailService;
    private static $notificationService;
    private static $pollService;
    private static $purgeService;
    private static $securityService;
    private static $sessionService;

    public static function init(Connection $connect, \Smarty $smarty) {
        self::$connect = $connect;
        self::$smarty = $smarty;
    }

    public static function adminPoll() {
        if (self::$adminPollService === null) {
            self::$adminPollService = new AdminPollService(self::$connect, self::poll(), self::log());
        }
        return self::$adminPollService;
    }

    public static function input() {
        if (self::$inputService === null) {
            self::$inputService = new InputService();
        }
        return self::$inputService;
    }

    public static function log() {
        if (self::$logService === null) {
            self::$logService = new LogService();
        }
        return self::$logService;
    }

    public static function mail() {
        if (self::$mailService === null) {
            self::$mailService = new MailService($config['use_smtp'], $config['smtp_options'], $config['use_sendmail']);
        }
        return self::$mailService;
    }

    public static function notification() {
        if (self::$notificationService === null) {
            self::$notificationService = new NotificationService(self::mail(), self::$smarty);
        }
        return self::$notificationService;
    }

    public static function poll() {
        if (self::$pollService === null) {
            self::$pollService = new PollService(self::log(), self::notification(), self::session(), self::purge());
        }
        return self::$pollService;
    }

    public static function purge() {
        if (self::$purgeService === null) {
            self::$purgeService = new PurgeService(self::$connect, self::log());
        }
        return self::$purgeService;
    }

    public static function session() {
        if (self::$sessionService === null) {
            self::$sessionService = new SessionService();
        }
        return self::$sessionService;
    }

    public static function security() {
        if (self::$securityService === null) {
            self::$securityService = new SecurityService();
        }
        return self::$securityService;
    }
}
