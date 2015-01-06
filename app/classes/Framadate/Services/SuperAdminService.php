<?php
namespace Framadate\Services;

use Framadate\FramaDB;

/**
 * The class provides action for application administrators.
 *
 * @package Framadate\Services
 */
class SuperAdminService {

    private $connect;

    function __construct(FramaDB $connect) {
        $this->connect = $connect;
    }

    /**
     * Return the list of all polls.
     *
     * @return array All the polls
     */
    public function findAllPolls() {
        return $this->connect->findAllPolls();
    }

}
 