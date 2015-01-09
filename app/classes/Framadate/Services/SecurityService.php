<?php
namespace Framadate\Services;

use Framadate\Security\Token;

class SecurityService {

    function __construct() {
    }

    function getToken($tokan_name) {
        if (!isset($_SESSION['token']) || !isset($_SESSION['token'][$tokan_name])) {
            $_SESSION['token'][$tokan_name] = new Token($tokan_name, 60*5);
        }

        return $_SESSION['token'][$tokan_name]->getValue();
    }

}
 