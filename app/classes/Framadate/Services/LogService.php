<?php
namespace Framadate\Services;

/**
 * This service provides a standard way to log some informations.
 *
 * @package Framadate\Services
 */
class LogService {

    private $output;

    function __construct($output) {
        $this->output = $output;
    }

    function log($tag, $message) {
        error_log('[' . $tag . '] ' . $message, 3, $this->output);
    }

}
 