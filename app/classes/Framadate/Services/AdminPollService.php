<?php
namespace Framadate\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Form;
use Framadate\Repositories\RepositoryFactory;
use stdClass;

/**
 * Class AdminPollService
 *
 * @package Framadate\Services
 */
class AdminPollService {
    private $connect;
    private $pollService;
    private $logService;

    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    function __construct(Connection $connect, PollService $pollService, LogService $logService) {
        $this->connect = $connect;
        $this->pollService = $pollService;
        $this->logService = $logService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    /**
     * @param Form $poll
     * @return bool
     */
    public function updatePoll($poll) {
        if ($poll->end_date < $poll->creation_date) {
            $poll->end_date = $poll->creation_date;
        } elseif ($poll->end_date > $this->pollService->maxExpiryDate()) {
            $poll->end_date = $this->pollService->maxExpiryDate();
        }

        return $this->pollRepository->update($poll);
    }

    /**
     * Delete a comment from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $comment_id int The ID of the comment
     * @return mixed true is action succeeded
     */
    function deleteComment($poll_id, $comment_id) {
        return $this->commentRepository->deleteById($poll_id, $comment_id);
    }

    /**
     * Remove all comments of a poll.
     *
     * @param $poll_id int The ID a the poll
     * @return bool|null true is action succeeded
     */
    function cleanComments($poll_id) {
        $this->logService->log("CLEAN_COMMENTS", "id:$poll_id");
        return $this->commentRepository->deleteByPollId($poll_id);
    }

    /**
     * Delete a vote from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $vote_id int The ID of the vote
     * @return mixed true is action succeeded
     */
    function deleteVote($poll_id, $vote_id) {
        return $this->voteRepository->deleteById($poll_id, $vote_id);
    }

    /**
     * Remove all votes of a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool|null true is action succeeded
     */
    function cleanVotes($poll_id) {
        $this->logService->log('CLEAN_VOTES', 'id:' . $poll_id);
        return $this->voteRepository->deleteByPollId($poll_id);
    }

    /**
     * Delete the entire given poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    function deleteEntirePoll($poll_id) {
        $poll = $this->pollRepository->findById($poll_id);
        $this->logService->logEntries(
            'DELETE_POLL',
            [
                "id:$poll->id",
                "format:$poll->format",
                "admin:$poll->admin_name",
                "mail:$poll->admin_mail"
            ]
        );

        // Delete the entire poll
        $this->voteRepository->deleteByPollId($poll_id);
        $this->commentRepository->deleteByPollId($poll_id);
        $this->slotRepository->deleteByPollId($poll_id);
        $this->pollRepository->deleteById($poll_id);

        return true;
    }

    /**
     * Delete a slot from a poll.
     *
     * @param object $poll The ID of the poll
     * @param object $slot The slot informations (datetime + moment)
     * @return bool true if action succeeded
     */
    public function deleteDateSlot($poll, $slot) {
        $this->logService->logEntries(
            'DELETE_SLOT',
            ["id:$poll->id", 'slot:' . json_encode($slot)]
        );

        $datetime = $slot->title;
        $moment = $slot->moment;

        $slots = $this->pollService->allSlotsByPoll($poll);

        // We can't delete the last slot
        if ($poll->format === 'D' && count($slots) === 1 && strpos($slots[0]->moments, ',') === false) {
            return false;
        } elseif ($poll->format === 'A' && count($slots) === 1) {
            return false;
        }

        $index = 0;
        $indexToDelete = -1;
        $newMoments = [];

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            $moments = explode(',', $aSlot->moments);

            foreach ($moments as $rowMoment) {
                if ($datetime === $aSlot->title) {
                    if ($moment === $rowMoment) {
                        $indexToDelete = $index;
                    } else {
                        $newMoments[] = $rowMoment;
                    }
                }
                $index++;
            }
        }

        // Remove votes
        $this->connect->beginTransaction();
        $this->voteRepository->deleteByIndex($poll->id, $indexToDelete);
        if (count($newMoments) > 0) {
            $this->slotRepository->update($poll->id, $datetime, implode(',', $newMoments));
        } else {
            $this->slotRepository->deleteByDateTime($poll->id, $datetime);
        }
        $this->connect->commit();

        return true;
    }

    public function deleteClassicSlot($poll, $slot_title) {
        $this->logService->logEntries(
            ['DELETE_SLOT', "id:$poll->id", "slot:$slot_title"]
        );

        $slots = $this->pollService->allSlotsByPoll($poll);

        if (count($slots) === 1) {
            return false;
        }

        $index = 0;
        $indexToDelete = -1;

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            if ($slot_title === $aSlot->title) {
                $indexToDelete = $index;
            }
            $index++;
        }

        // Remove votes
        $this->connect->beginTransaction();
        $this->voteRepository->deleteByIndex($poll->id, $indexToDelete);
        $this->slotRepository->deleteByDateTime($poll->id, $slot_title);
        $this->connect->commit();

        return true;
    }

    /**
     * Add a new slot to a date poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new slot if no one exists for the given date</li>
     *  <li>Create a new moment if a slot already exists for the given date</li>
     * </ul>
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime
     * @param $new_moment string The moment's name
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     * @throws ConnectionException
     */
    public function addDateSlot($poll_id, $datetime, $new_moment) {
        $this->logService->logEntries(
            'ADD_COLUMN',
            ["id:$poll_id", "datetime:$datetime", "moment:$new_moment"]
        );

        try {
            $slots = $this->slotRepository->listByPollId($poll_id);
            $result = $this->findInsertPosition($slots, $datetime);
        } catch (DBALException $e) {
            $this->logService->log('ERROR', "Database error, couldn't find slot insert position" . $e->getMessage());
            return;
        }

        try {
            // Begin transaction
            $this->connect->beginTransaction();

            if ($result->slot !== null) {
                $slot = $result->slot;
                $moments = explode(',', $slot->moments);

                // Check if moment already exists (maybe not necessary)
                if (in_array($new_moment, $moments, true)) {
                    throw new MomentAlreadyExistsException($slot, $new_moment);
                }

                // Update found slot
                $moments[] = $new_moment;
                $this->slotRepository->update($poll_id, $datetime, implode(',', $moments));
            } else {
                $this->slotRepository->insert($poll_id, $datetime, $new_moment);
            }

            $this->voteRepository->insertDefault($poll_id, $result->insert);

            // Commit transaction
            $this->connect->commit();
        } catch (DBALException $e) {
            $this->logService->log('ERROR', "Database error, couldn't insert date slot" . $e->getMessage());
            $this->connect->rollBack();
        }
    }

    /**
     * Add a new slot to a classic poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new slot if no one exists for the given title</li>
     * </ul>
     *
     * @param $poll_id int The ID of the poll
     * @param $title int The title
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     * @throws ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addClassicSlot($poll_id, $title) {
        $this->logService->logEntries(
            'ADD_COLUMN',
            ["id:$poll_id", "title:$title"]
        );

        $slots = $this->slotRepository->listByPollId($poll_id);

        // Check if slot already exists
        $titles = array_map(function ($slot) {
            return $slot->title;
        }, $slots);
        if (in_array($title, $titles, true)) {
            // The moment already exists
            throw new MomentAlreadyExistsException();
        }

        // Begin transaction
        $this->connect->beginTransaction();

        // New slot
        $this->slotRepository->insert($poll_id, $title, null);
        // Set default votes
        $this->voteRepository->insertDefault($poll_id, count($slots));

        // Commit transaction
        $this->connect->commit();
    }

    /**
     * This method find where to insert a datatime+moment into a list of slots.<br/>
     * Return the {insert:X}, where X is the index of the moment into the whole poll (ex: X=0 => Insert to the first column).
     * Return {slot:Y}, where Y is not null if there is a slot existing for the given datetime.
     *
     * @param $slots array All the slots of the poll
     * @param $datetime int The datetime of the new slot
     * @return stdClass An object like this one: {insert:X, slot:Y} where Y can be null.
     */
    private function findInsertPosition($slots, $datetime) {
        $result = new stdClass();
        $result->slot = null;
        $result->insert = 0;

        // Sort slots before searching where to insert
        $this->pollService->sortSlorts($slots);

        // Search where to insert new column
        foreach ($slots as $k=>$slot) {
            $rowDatetime = (int) $slot->title;
            $moments = explode(',', $slot->moments);

            if ($datetime === $rowDatetime) {
                // Here we have to insert at the end of a slot
                $result->insert += count($moments);
                $result->slot = $slot;
                break;
            }

            if ($datetime < $rowDatetime) {
                // We have to insert before this slot
                break;
            }
            $result->insert += count($moments);
        }

        return $result;
    }
}
