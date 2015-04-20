<?php


namespace Framadate\Services;

use Framadate\Security\PasswordHasher;

/**
 * Class RestrictedAccessService
 *
 * This class allow to manage the user access to the polls restricted by a password
 *
 * @package Framadate\Services
 */
class RestrictedAccessService {
    const SESSION_KEY = "poll_password";

    function __construct() {}

    /**
     * Verify that the current user has access to a poll.
     *
     * @param string $pollId The poll id that the current user has access to.
     * @return bool
     */
    public function hasAccess($pollId) {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array();
        }
        if (isset($_SESSION[self::SESSION_KEY][$pollId]) && $_SESSION[self::SESSION_KEY][$pollId] === true) {
            return true;
        }
        return false;
    }

    /**
     * Compare the poll's hashed password and the password given by the user, and set access accordingly.
     *
     * @param \stdClass $poll The poll object.
     * @param string $password The password to verify
     * @return bool
     */
    public function compareAccess(\stdClass $poll, $password) {
        if (PasswordHasher::verify($password, $poll->password_hash)) {
            $this->setAccess($poll->id, true);
        } else {
            $this->setAccess($poll->id, false);
        }
        return $this->hasAccess($poll->id);
    }

    private function setAccess($pollId, $access) {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = array();
        }
        $_SESSION[self::SESSION_KEY][$pollId] = $access;
    }
} 