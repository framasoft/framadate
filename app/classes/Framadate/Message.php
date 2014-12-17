<?php
namespace Framadate;

class Message {

    var $type;
    var $message;

    function __construct($type, $message) {
        $this->type = $type;
        $this->message = $message;
    }

}
 