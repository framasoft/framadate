<?php
namespace Framadate\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Framadate\Entity\Choice;
use Framadate\Entity\DateChoice;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Entity\Poll;
use Framadate\Repository\ChoiceRepository;
use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
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
    private $choiceRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(Connection $connect, PollService $pollService, LoggerInterface $logger, PollRepository $pollRepository, ChoiceRepository $choiceRepository, VoteRepository $voteRepository, CommentRepository $commentRepository)
    {
        $this->connect = $connect;
        $this->pollService = $pollService;
        $this->logger = $logger;
        $this->pollRepository = $pollRepository;
        $this->choiceRepository = $choiceRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @param Poll $poll
     * @return bool
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

        try {
            // Delete the entire poll
            $this->voteRepository->deleteByPollId($poll_id);
            $this->commentRepository->deleteByPollId($poll_id);
            $this->choiceRepository->deleteByPollId($poll_id);
            $this->pollRepository->deleteById($poll_id);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Delete a choice from a poll.
     *
     * @param Poll $poll The poll
     * @param DateChoice $choice
     * @return bool true if action succeeded
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function deleteDateChoice(Poll $poll, DateChoice $choice)
    {
        $this->logger->info('DELETE_choice: id:' . $poll->getId(), [$choice]);

        $datetime = $choice->getDate();
        $moment = $choice->getMoments();

        $choices = $this->pollService->allChoicesByPoll($poll);

        // We can't delete the last choice
        if (
            ($poll->isDate() && count($choices) === 1 && strpos($choices[0]->getMoments(), ',') === false) ||
            (!$poll->isDate() && count($choices) === 1)) {
            $this->logger->info("We can't delete the last choice", [$choices]);
            return false;
        }

        $index = 0;
        $indexToDelete = -1;
        $newMoments = [];

        // Search the index of the choice to delete
        foreach ($choices as $choice) {
            /** @var DateChoice $choice */

            foreach ($choice->getMoments() as $rowMoment) {
                if ($datetime === $choice->getDate()) {
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
            $this->choiceRepository->update($poll->getId(), $datetime, implode(',', $newMoments));
        } else {
            $this->choiceRepository->deleteByDateTime($poll->getId(), $datetime);
        }
        $this->connect->commit();

        return true;
    }

    /**
     * @param Poll $poll
     * @param Choice $choice
     * @return bool
     */
    public function deleteClassicChoice(Poll $poll, Choice $choice)
    {
        $this->logger->info('DELETE_choice: id:' . $poll->getId() . ', choice:' . $choice->getName());

        $choices = $this->pollService->allChoicesByPoll($poll);

        if (count($choices) === 1) {
            return false;
        }

        $index = 0;
        $indexToDelete = -1;

        // Search the index of the choice to delete
        foreach ($choices as $aChoice) {
            /** @var $aChoice Choice */
            if ($choice->getName() === $aChoice->getName()) {
                $indexToDelete = $index;
            }
            $index++;
        }

        try {
            // Remove votes
            $this->connect->beginTransaction();
            $this->voteRepository->deleteByIndex($poll->getId(), $indexToDelete);
            $this->choiceRepository->deleteByTitle($poll->getId(), $choice->getName());
            $this->connect->commit();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }

        return true;
    }

    /**
     * Add a new choice to a date poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new choice if no one exists for the given date</li>
     *  <li>Create a new moment if a choice already exists for the given date</li>
     * </ul>
     *
     * @param DateChoice $choice
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     */
    public function addDateChoice(DateChoice $choice)
    {
        $this->logger->info('ADD_COLUMN: id:' . $choice->getPollId() . ', datetime:' . $choice->getDate()->getTimestamp() . ', moment:' , [$choice->getMoments()]);

        try {
            $choices = $this->choiceRepository->listByPollId($choice->getPollId(), true);
            $this->logger->debug("choices", [$choices]);

            $result = $this->findInsertPosition($choices, $choice->getDate());
            $this->logger->debug("insert pos result", [$result]);

            // Begin transaction
            $this->connect->beginTransaction();

            if ($result->choice !== null) {
                /** @var DateChoice $existingChoice */
                $existingChoice = $result->choice;

                // Check if moment already exists (maybe not necessary)
                if (count(array_intersect($choice->getMoments(), $existingChoice->getMoments())) > 0) {
                    throw new MomentAlreadyExistsException();
                }

                // Update found choice
                //$choice->addMoment($new_moment);
                $this->choiceRepository->update($choice->getPollId(), $choice->getDate(), implode(',', array_merge($existingChoice
                ->getMoments(), $choice->getMoments())));
            } else {
                $this->choiceRepository->insertDateChoice($choice);
            }

            $this->voteRepository->insertDefault($choice->getPollId(), $result->insert);

            // Commit transaction
            $this->connect->commit();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Add a new choice to a classic poll. And insert default values for user's votes.
     * <ul>
     *  <li>Create a new choice if no one exists for the given title</li>
     * </ul>
     *
     * @param Choice $choice
     * @throws MomentAlreadyExistsException When the moment to add already exists in database
     */
    public function addClassicChoice(Choice $choice)
    {
        $this->logger->info('ADD_COLUMN: id:' . $choice->getPollId() . ', title:' . $choice->getName());

        try {
            $choices = $this->choiceRepository->listByPollId($choice->getPollId());

            // Check if choice already exists
            $titles = array_map(
                function ($choice) {
                    /** @var $choice Choice */
                    return $choice->getName();
                },
                $choices
            );
            if (in_array($choice->getName(), $titles, true)) {
                // The moment already exists
                throw new MomentAlreadyExistsException();
            }

            // Begin transaction
            $this->connect->beginTransaction();

            // New choice
            $this->choiceRepository->insertChoice($choice);
            // Set default votes
            $this->voteRepository->insertDefault($choice->getPollId(), count($choices));

            // Commit transaction
            $this->connect->commit();
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * This method find where to insert a datatime+moment into a list of choices.<br/>
     * Return the {insert:X}, where X is the index of the moment into the whole poll (ex: X=0 => Insert to the first column).
     * Return {choice:Y}, where Y is not null if there is a choice existing for the given datetime.
     *
     * @param Choice[] $choices All the choices of the poll
     * @param \DateTime $datetime The datetime of the new choice
     * @return \stdClass An object like this one: {insert:X, choice:Y} where Y can be null.
     */
    private function findInsertPosition(array $choices, \DateTime $datetime)
    {
        $result = new \stdClass();
        $result->choice = null;
        $result->insert = 0;

        // Sort choices before searching where to insert
        $this->pollService->sortChoices($choices);

        // Search where to insert new column
        foreach ($choices as $choice) {
            /** @var DateChoice $choice */

            $this->logger->debug('processing choice', [$choice]);

            $rowDatetime = $choice->getDate();
            $moments = $choice->getMoments();

            $this->logger->debug('found moments', [$moments]);
            $this->logger->debug('comparing datetime and rowdatetime', [$datetime, $rowDatetime]);

            if ($datetime == $rowDatetime) {
                // Here we have to insert at the end of a choice
                $this->logger->info("We chose to insert at the end of choice", [$choice]);
                $result->insert += count($moments);
                $result->choice = $choice;
                break;
            } elseif ($datetime < $rowDatetime) {
                // We have to insert before this choice
                $this->logger->info("We chose to insert before the choice", [$choice]);
                break;
            }
            $result->insert += count($moments);
        }

        return $result;
    }
}
