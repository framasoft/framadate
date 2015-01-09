<?php
namespace Framadate\Security;

class Token {

    private $tokan_name;
    private $time;
    private $value;

    function __construct($tokan_name, $time) {
       $this->tokan_name = $tokan_name;
       $this->time = $time;
       $this->value = $this->generate();
    }

    private function generate() {
        // TODO
    }

}
 