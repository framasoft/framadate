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
     * @param $page int The page index (O = first page)
     * @param $limit int The limit size
     * @return array ['polls' => The {$limit} polls, 'count' => Total count]
     * polls, 'count' => Total count]
     */
    public function findAllPolls($page, $limit) {
        return $this->connect->findAllPolls($page * $limit, $limit);
    }

}
 