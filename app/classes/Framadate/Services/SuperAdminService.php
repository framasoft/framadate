<?php
namespace Framadate\Services;

use Framadate\Repositories\RepositoryFactory;

/**
 * The class provides action for application administrators.
 *
 * @package Framadate\Services
 */
class SuperAdminService {

    private $pollRepository;

    function __construct() {
        $this->pollRepository = RepositoryFactory::pollRepository();
    }

    /**
     * Return the list of all polls.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @param int $page The page index (O = first page)
     * @param int $limit The limit size
     * @return array ['polls' => The {$limit} polls, 'count' => Entries found by the query, 'total' => Total count]
     */
    public function findAllPolls($search, $page, $limit) {
        $start = $page * $limit;
        $polls = $this->pollRepository->findAll($search);
        $total = $this->pollRepository->count();


        return ['polls' => array_slice($polls, $start, $limit), 'count' => count($polls), 'total' => $total];
    }

}
 