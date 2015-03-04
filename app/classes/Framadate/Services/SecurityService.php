<?php
namespace Framadate\Services;

use Framadate\Security\Token;

class SecurityService {

    function __construct() {
    }

    /**
     * Get a CSRF token by name, or (re)create it.
     *
     * It creates a new token if :
     * <ul>
     *  <li>There no token with the given name in session</li>
     *  <li>The token time is in past</li>
     * </ul>
     *
     * @param $tokan_name string The name of the CSRF token
     * @return Token The token
     */
    function getToken($tokan_name) {
        if (!isset($_SESSION['tokens'])) {
            $_SESSION['tokens'] = [];
        }
        if (!isset($_SESSION['tokens'][$tokan_name]) || $_SESSION['tokens'][$tokan_name]->isGone()) {
            $_SESSION['tokens'][$tokan_name] = new Token();
        }

        return $_SESSION['tokens'][$tokan_name]->getValue();
    }

    /**
     * Check if a given value is corresponding to the token in session.
     *
     * @param $tokan_name string Name of the token
     * @param $csrf string Value to check
     * @return bool true if the token is well checked
     */
    public function checkCsrf($tokan_name, $csrf) {
        $checked = $_SESSION['tokens'][$tokan_name]->getValue() === $csrf;

        if($checked) {
            unset($_SESSION['tokens'][$tokan_name]);
        }

        return $checked;
    }

}
 