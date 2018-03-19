<?php
namespace Framadate\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Framadate\Entity\DateSlot;
use Framadate\Entity\Slot;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Entity\Poll;
use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
use Framadate\Repository\SlotRepository;
use Framadate\Repository\VoteRepository;
use Psr\Log\LoggerInterface;

/**
 * Class AdminPollService
 *
 * @package Framadate\Services
 */
class AdminPollService
{

    /**
     * @var Connection
     */
    private $connect;

    /**
     * @var PollService
     */
    private $pollService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(Connection $connect, PollService $pollService, LoggerInterface $logger, PollRepository $pollRepository, SlotRepository $slotRepository, VoteRepository $voteRepository, CommentRepository $commentRepository)
    {
        $this->connect = $connect;
        $this->pollService = $pollService;
        $this->logger = $logger;
        $this->pollRepository = $pollRepository;
        $this->slotRepository = $slotRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param Poll $poll
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function updatePoll(Poll $poll)
    {
        if ($poll->getEndDate() > $poll->getCreationDate()) {
            return $this->pollRepository->update($poll);
        }
        return false;
    }

    /**
     * Delete a vote from a poll.
     *
     * @param $poll_id int The ID of the poll
     * @param $vote_id int The ID of the vote
     * @return mixed true is action succeeded
     */
    public function deleteVote($poll_id, $vote_id)
    {
        return $this->voteRepository->deleteById($poll_id, $vote_id);
    }

    /**
     * Remove all votes of a poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool|null true is action succeeded
     */
    public function cleanVotes($poll_id)
    {
        $this->logger->info('CLEAN_VOTES id:' . $poll_id);
        return $this->voteRepository->deleteByPollId($poll_id);
    }

    /**
     * Delete the entire given poll.
     *
     * @param $poll_id int The ID of the poll
     * @return bool true is action succeeded
     */
    public function deleteEntirePoll($poll_id)
    {
        $poll = $this->pollRepository->findById($poll_id);
        $this->logger->log('info', "DELETE_POLL : id:" . $poll->getId() . ", format:" . $poll->getFormat() . ", admin:" . $poll->getAdminMail() . ", mail:" . $poll->getAdminMail());

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
     * @param Poll $poll The poll
     * @param DateSlot $slot The slot informations (datetime + moment)
     * @return bool true if action succeeded
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function deleteDateSlot(Poll $poll, DateSlot $slot)
    {
        $this->logger->info('DELETE_SLOT: id:' . $poll->getId(), [$slot]);

        $datetime = $slot->getTitle();
        $moment = $slot->getMoments();

        $slots = $this->pollService->allSlotsByPoll($poll);

        // We can't delete the last slot
        if (
            ($poll->isDate() && count($slots) === 1 && strpos($slots[0]->moments, ',') === false) ||
            (!$poll->isDate() && count($slots) === 1)) {
            $this->logger->info("We can't delete the last slot", [$slots]);
            return false;
        }

        $index = 0;
        $indexToDelete = -1;
        $newMoments = [];

        // Search the index of the slot to delete
        foreach ($slots as $aSlot) {
            /** @var DateSlot $aSlot */
            $moments = explode(',', $aSlot->getMoments());

            foreach ($moments as $rowMoment) {
                if ($datetime === $aSlot->getTitle()) {
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
        $this->voteRepository->deleteByIndex($poll->getId(), $indexToDelete);
        if (count($newMoments) > 0) {
            $this->slotRepository->update($poll->getId(), $datetime, implode(',', $newMoments));
        } else {
            $this->slotRepository->deleteByDateTime($poll->getId(), $datetime);
        }
        $this->connect->commit();

        return true;
    }

    public function deleteClassicSlot($poll, $slot_title)
    {
        $this->logger->info('DELETE_SLOT: id:' . $poll->id . ', slot:' . $slot_title);

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
     */
    public function addDateSlot($poll_id, $datetime, $new_moment)
    {
        $this->logger->info('ADD_COLUMN: id:' . $poll_id . ', datetime:' . $datetime . ', moment:' . $new_moment);

        try {
            $slots = $this->slotRepository->listByPollId($poll_id, true);
            $this->logger->debug("slots", [$slots]);

            $result = $this->findInsertPosition($slots, $datetime);
            $this->logger->debug("insert pos result", [$result]);

            // Begin transaction
            $this->connect->beginTransaction();

            if ($result->slot !== null) {
                /** @var DateSlot $slot */
                $slot = $result->slot;
                $moments = explode(',', $slot->getMoments());

                // Check if moment already exists (maybe not necessary)
                if (in_array($new_moment, $moments, true)) {
                    throw new MomentAlreadyExistsException();
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
            $this->logger->error($e->getMessage());
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
     */
    public function addClassicSlot($poll_id, $title)
    {
        $this->logger->info('ADD_COLUMN: id:' . $poll_id . ', title:' . $title);

        try {
            $slots = $this->slotRepository->listByPollId($poll_id);

            // Check if slot already exists
            $titles = array_map(
                function ($slot) {
                    return $slot->title;
                },
                $slots
            );
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
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * This method find where to insert a datatime+moment into a list of slots.<br/>
     * Return the {insert:X}, where X is the index of the moment into the whole poll (ex: X=0 => Insert to the first column).
     * Return {slot:Y}, where Y is not null if there is a slot existing for the given datetime.
     *
     * @param $slots array All the slots of the poll
     * @param $datetime int The datetime of the new slot
     * @return \stdClass An object like this one: {insert:X, slot:Y} where Y can be null.
     */
    private function findInsertPosition($slots, $datetime)
    {
        $result = new \stdClass();
        $result->slot = null;
        $result->insert = 0;

        // Sort slots before searching where to insert
        $this->pollService->sortSlorts($slots);

        // Search where to insert new column
        foreach ($slots as $slot) {
            /** @var DateSlot $slot */

            $this->logger->debug('processing slot', [$slot]);

            $rowDatetime = $slot->getTitle();
            $moments = explode(',', $slot->getMoments());

            $this->logger->debug('found moments', [$moments]);
            $this->logger->debug('comparing datetime and rowdatetime', [$datetime, $rowDatetime]);

            if ($datetime === $rowDatetime) {
                // Here we have to insert at the end of a slot
                $result->insert += count($moments);
                $result->slot = $slot;
                break;
            } elseif ($datetime < $rowDatetime) {
                // We have to insert before this slot
                break;
            }
            $result->insert += count($moments);
        }

        return $result;
    }
}
