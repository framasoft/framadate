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

    public function __construct() {
        $this->pollRepository = RepositoryFactory::pollRepository();
    }

    /**
     * Return the list of all polls.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
     * @param int $page The page index (O = first page)
     * @param int $limit The limit size
     * @return array ['polls' => The {$limit} polls, 'count' => Entries found by the query, 'total' => Total count]
     */
    public function findAllPolls(array $search, int $page, int $limit): array
    {
        $start = $page * $limit;
        $polls = $this->pollRepository->findAll($search, $start, $limit);
        $count = $this->pollRepository->count($search);
        $total = $this->pollRepository->count();

        return ['polls' => $polls, 'count' => $count, 'total' => $total];
    }
}
