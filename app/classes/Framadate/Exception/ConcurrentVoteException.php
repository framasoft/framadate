<?php
namespace Framadate\Exception;

/**
 * Class ConcurrentVoteException
 *
 * Thrown when a poll has a maximum votes constraint for options, and a vote happened since the poll was rendered
 */
class ConcurrentVoteException extends \Exception {
}
