<?php
namespace Framadate\Security;

class Token {

    private $time;
    private $value;

    function __construct() {
        $this->time = time() + TOKEN_TIME;
        $this->value = $this->generate();
    }

    private function generate() {
        return sha1(uniqid(mt_rand(), true));
    }

    public function getTime() {
        return $this->time;
    }

    public function getValue() {
        return $this->value;
    }

    public function isGone() {
        return $this->time < time();
    }

    public function check($value) {
        return $value === $this->value;
    }

}
 