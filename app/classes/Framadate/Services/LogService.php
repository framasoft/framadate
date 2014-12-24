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

    /**
     * Log a message to the log file.
     *
     * @param $tag string A tag is used to quickly found a message when reading log file
     * @param $message string some message
     */
    function log($tag, $message) {
        error_log('[' . $tag . '] ' . $message . "\n", 3, $this->output);
    }

}
 