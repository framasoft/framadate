<?php
namespace Framadate\Services;

/**
 * This service provides a standard way to log some informations.
 *
 * @package Framadate\Services
 */
class LogService {
    function __construct() {
    }

    /**
     * Log a message to the log file.
     *
     * @param $tag string A tag is used to quickly found a message when reading log file
     * @param $message string some message
     */
    function log($tag, $message) {
        error_log(date('Ymd His') . ' [' . $tag . '] ' . $message . "\n", 3, ROOT_DIR . LOG_FILE);
    }

    /**
     * Log a list of entries as a single message to the log file.
     *
     * @param $tag string A tag is used to quickly found a message when reading log file
     * @param $entries array some entries to join with comma into a single message
     */
    function logEntries($tag, $entries) {
        $escapeCommas = function($value) {
            return str_replace(',', '-', $value);
        };
        $message = join(', ', array_map($escapeCommas, $entries));

        error_log(date('Ymd His') . ' [' . $tag . '] ' . $message . "\n", 3, ROOT_DIR . LOG_FILE);
    }
}
