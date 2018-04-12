<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Services;

use Doctrine\DBAL\DBALException;
use Framadate\Entity\Choice;
use Framadate\Entity\Comment;
use Framadate\Entity\DateChoice;
use Framadate\Entity\Moment;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Entity\Poll;
use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
use Framadate\Repository\ChoiceRepository;
use Framadate\Repository\VoteRepository;
use Framadate\Security\Token;
use Framadate\Entity\Vote;
use Psr\Log\LoggerInterface;

class PollService
{
    private $logService;

    private $pollRepository;
    private $choiceRepository;
    private $voteRepository;
    private $commentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LogService $logService, LoggerInterface $logger, PollRepository $pollRepository, ChoiceRepository $choiceRepository, VoteRepository $voteRepository, CommentRepository $commentRepository)
    {
        $this->logService = $logService;
        $this->pollRepository = $pollRepository;
        $this->choiceRepository = $choiceRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
    }

    /**
     * Find a poll from its ID.
     *
     * @param $poll_id int The ID of the poll
     * @return Poll
     * @throws DBALException
     */
    public function findById($poll_id)
    {
        if (preg_match(Poll::POLL_REGEX, $poll_id)) {
            return $this->pollRepository->findById($poll_id);
        }

        return null;
    }

    /**
     * @param $admin_poll_id
     * @return Poll|null
     */
    public function findByAdminId(string $admin_poll_id)
    {
        try {
            if (preg_match(Poll::ADMIN_POLL_REGEX, $admin_poll_id)) {
                return $this->pollRepository->findByAdminId($admin_poll_id);
            }
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param $poll_id
     * @return array
     */
    public function allCommentsByPollId($poll_id)
    {
        try {
            return $this->commentRepository->findAllByPollId($poll_id);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return [];
        }
    }

    /**
     * @param $poll_id
     * @return array
     */
    public function allVotesByPollId($poll_id)
    {
        return $this->voteRepository->allUserVotesByPollId($poll_id);
    }

    /**
     * @param Poll $poll
     * @return Choice[]
     */
    public function allChoicesByPoll(Poll $poll)
    {
        try {
            $choices = $this->choiceRepository->listByPollId($poll->getId(), $poll->getFormat() === 'D');

            if ($poll->isDate()) {
                $this->sortChoices($choices);
            }
            return $choices;
        } catch (DBALException $e) {
            // log exception
            return [];
        }
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @param $name
     * @param $choices
     * @param $choices_hash
     * @return bool
     */
    public function updateVote($poll_id, $vote_id, $name, $choices, $choices_hash)
    {
        $this->checkVoteConstraints($choices, $poll_id, $choices_hash, $name, $vote_id);

        // Update vote
        $choices = implode($choices);
        return $this->voteRepository->update($poll_id, $vote_id, $name, $choices);
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $choices
     * @param $choices_hash
     * @return Vote
     */
    public function addVote($poll_id, $name, $choices, $choices_hash)
    {
        $this->checkVoteConstraints($choices, $poll_id, $choices_hash, $name);

        // Insert new vote
        $choices = implode($choices);
        $token = $this->random(16);
        try {
            return $this->voteRepository->insert($poll_id, $name, $choices, $token);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * @param Comment $comment
     * @return Comment
     */
    public function addComment(Comment $comment)
    {
        try {
            if ($existingComment = $this->commentRepository->exists($comment)) {
                return $existingComment;
            }
            return $this->commentRepository->insert($comment);
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * @param Poll $poll
     * @return Poll
     */
    public function createPoll(Poll $poll)
    {
        // Generate poll IDs, loop while poll ID already exists

        if ($poll->getId() === null) { // User want us to generate an id for him
            do {
                $poll->setId($this->random(16));
            } while ($this->pollRepository->existsById($poll->getId()));
            $poll->setAdminId($poll->getId() . $this->random(8));
        } else { // User have choosen the poll id
            do {
                $poll->setAdminId($this->random(24));
            } while ($this->pollRepository->existsByAdminId($poll->getAdminId()));
        }

        try {
            // Insert poll + choices
            $this->pollRepository->beginTransaction();
            $this->pollRepository->insertPoll($poll);
            $this->choiceRepository->insertchoices($poll->getId(), $poll->getChoices());
            $this->pollRepository->commit();


            $this->logger->info(
                'CREATE_POLL' . 'id:' . $poll->getId() . ', title: ' . $poll->getTitle(
                ) . ', format:' . $poll->getFormat() . ', admin:' . $poll->getAdminName(
                ) . ', mail:' . $poll->getAdminMail()
            );
            // $this->logService->log('CREATE_POLL', 'id:' . $poll_id . ', title: ' . $form->getTitle() . ', format:' . $form->getFormat() . ', admin:' . $form->getAdminName() . ', mail:' . $form->getAdminMail());

            return $poll;
        } catch (DBALException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function existsById($poll_id)
    {
        return $this->pollRepository->existsById($poll_id);
    }

    /**
     * @param $mail
     * @return array
     * @throws DBALException
     */
    public function findAllByAdminMail($mail)
    {
        return $this->pollRepository->findAllByAdminMail($mail);
    }

    public function computeBestChoices($votes, Poll $poll)
    {
        $result = ['y' => [], 'inb' => []];

        if (0 === count($votes)) {
            // if there is no votes, calculates the number of slot

            $choices = $this->allChoicesByPoll($poll);

            if (!$poll->isDate()) {
                // poll format classic

                foreach ($choices as $choice) {
                    $result['y'][] = 0;
                    $result['inb'][] = 0;
                }
            } else {
                // poll format date

                $choices = $this->splitChoices($choices);

                foreach ($choices as $choice) {
                    foreach ($choice->moments as $_) {
                        $result['y'][] = 0;
                        $result['inb'][] = 0;
                    }
                }
            }
        } else {
            // if there is votes

            foreach ($votes as $vote) {
                $choices = str_split($vote->choices);
                foreach ($choices as $i => $choice) {
                    if (!isset($result['y'][$i])) {
                        $result['inb'][$i] = 0;
                        $result['y'][$i] = 0;
                    }
                    if ($choice === "1") {
                        $result['inb'][$i]++;
                    }
                    if ($choice === "2") {
                        $result['y'][$i]++;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param Choice[] $choices The choices to hash
     * @return string The hash
     */
    public function hashChoices(array $choices)
    {
        return md5(array_reduce($choices, function ($carry, $item) {
            if ($item instanceof DateChoice) {
                return $carry . $item->getId() . '@' . implode(',', $item->getMoments()) . ';';
            }
            /** @var Choice $item */
            return $carry . $item->getId() . '@;';
        }));
    }

    /**
     * @param Vote[] $votes
     * @return array
     */
    public function splitVotes(array $votes)
    {
        $splitted = [];
        foreach ($votes as $vote) {
            /** @var Vote $vote */
            $obj = new \stdClass();
            $obj->id = $vote->getId();
            $obj->name = $vote->getName();
            $obj->uniqId = $vote->getUniqId();
            $obj->choices = str_split($vote->getChoices());

            $splitted[] = $obj;
        }

        return $splitted;
    }

    function splitChoices($choices)
    {
        $splitted = [];
        foreach ($choices as $choice) {
            /** @var DateChoice $choice */
            $obj = new \stdClass();
            $obj->day = $choice->getName();
            $obj->moments = $choice->getMoments();

            $splitted[] = $obj;
        }

        return $splitted;
    }


    /**
     * @return \DateTime The max timestamp allowed for expiry date
     */
    public function maxExpiryDate()
    {
        // TODO : inject proper config
        return (new \DateTime())->modify('+'. 60 .' day');
    }

    /**
     * @return \DateTime The min timestamp allowed for expiry date
     */
    public function minExpiryDate()
    {
        return (new \DateTime())->modify('+1 day');
    }

    /**
     * @param array $choices
     * @return array
     */
    public function sortChoices(array &$choices)
    {
        uasort($choices, function (Choice $a, Choice $b) {
            return $a->getName() > $b->getName();
        });
        return $choices;
    }

    /**
     * @param $length
     * @return string
     */
    private function random($length)
    {
        return Token::getToken($length);
    }

    /**
     * This method checks if the hash send by the user is the same as the computed hash.
     *
     * @param $poll /stdClass The poll
     * @param $choices_hash string The hash sent by the user
     * @throws ConcurrentEditionException Thrown when hashes are different
     */
    private function checkThatChoicesDidntChanged($poll, $choices_hash)
    {
        $choices = $this->allChoicesByPoll($poll);
        $hashed_choices = $this->hashChoices($choices);
        if ($choices_hash !== $hashed_choices) {
            $this->logger->info("ConcurrentEditionException : the two choices hash don't match", [$choices_hash, $hashed_choices]);
            throw new ConcurrentEditionException();
        }
    }

    /**
     * This method checks if the votes doesn't conflicts the maximum votes constraint
     *
     * @param $user_choice
     * @param Poll $poll
     * @param string $poll_id
     * @throws ConcurrentVoteException
     */
    private function checkMaxVotes($user_choice, Poll $poll, $poll_id)
    {
        $votes = $this->allVotesByPollId($poll_id);
        if (count($votes) <= 0) {
            return;
        }
        $best_choices = $this->computeBestChoices($votes, $poll);
        foreach ($best_choices['y'] as $i => $nb_choice) {
            // if for this option we have reached maximum value and user wants to add itself too
            if ($poll->getValueMax() > 0 && $nb_choice >= $poll->getValueMax() && $user_choice[$i] === "2") {
                throw new ConcurrentVoteException();
            }
        }
    }

    /**
     * @param $choices
     * @param $poll_id
     * @param $choices_hash
     * @param $name
     * @param string|bool $vote_id
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @throws DBALException
     */
    private function checkVoteConstraints($choices, $poll_id, $choices_hash, $name, $vote_id = false)
    {
        // Check if vote already exists with the same name
        if (false !== $vote_id) {
            $exists = $this->voteRepository->existsByPollIdAndName($poll_id, $name);
        } else {
            $exists = $this->voteRepository->existsByPollIdAndNameAndVoteId($poll_id, $name, $vote_id);
        }

        if ($exists) {
            throw new AlreadyExistsException();
        }

        $poll = $this->findById($poll_id);

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        $this->checkMaxVotes($choices, $poll, $poll_id);

        // Check if choices are still the same
        $this->checkThatChoicesDidntChanged($poll, $choices_hash);
    }
}
