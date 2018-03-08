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
use Framadate\Entity\DateSlot;
use Framadate\Entity\Slot;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Entity\Poll;
use Framadate\Repository\CommentRepository;
use Framadate\Repository\PollRepository;
use Framadate\Repository\SlotRepository;
use Framadate\Repository\VoteRepository;
use Framadate\Security\Token;
use Framadate\Entity\Vote;
use Psr\Log\LoggerInterface;

class PollService {
    private $logService;

    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    function __construct(LogService $logService, LoggerInterface $logger, PollRepository $pollRepository, SlotRepository $slotRepository, VoteRepository $voteRepository, CommentRepository $commentRepository) {
        $this->logService = $logService;
        $this->pollRepository = $pollRepository;
        $this->slotRepository = $slotRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
        $this->logger = $logger;
    }

    /**
     * Find a poll from its ID.
     *
     * @param $poll_id int The ID of the poll
     * @return Poll
     */
    function findById($poll_id) {
        if (preg_match(Poll::POLL_REGEX, $poll_id)) {
            return $this->pollRepository->findById($poll_id);
        }

        return null;
    }

    /**
     * @param $admin_poll_id
     * @return Poll|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByAdminId($admin_poll_id) {
        if (preg_match(Poll::ADMIN_POLL_REGEX, $admin_poll_id)) {
            return $this->pollRepository->findByAdminId($admin_poll_id);
        }

        return null;
    }

    /**
     * @param $poll_id
     * @return array
     */
    public function allCommentsByPollId($poll_id) {
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
    function allVotesByPollId($poll_id) {
        return $this->voteRepository->allUserVotesByPollId($poll_id);
    }

    /**
     * @param Poll $poll
     * @return array
     */
    function allSlotsByPoll(Poll $poll) {
        try {
            $slots = $this->slotRepository->listByPollId($poll->getId(), $poll->getFormat() === 'D');

            if ($poll->getFormat() === 'D') {
                $this->sortSlorts($slots);
            }
            return $slots;
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
     * @param $slots_hash
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @return bool
     */
    public function updateVote($poll_id, $vote_id, $name, $choices, $slots_hash) {
        $poll = $this->findById($poll_id);

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        $this->checkMaxVotes($choices, $poll, $poll_id);

        // Check if slots are still the same
        $this->checkThatSlotsDidntChanged($poll, $slots_hash);

        // Update vote
        $choices = implode($choices);
        return $this->voteRepository->update($poll_id, $vote_id, $name, $choices);
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @return Vote
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     */
    function addVote($poll_id, $name, $choices, $slots_hash) {
        $poll = $this->findById($poll_id);

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        $this->checkMaxVotes($choices, $poll, $poll_id);

        // Check if slots are still the same
        $this->checkThatSlotsDidntChanged($poll, $slots_hash);

        // Check if vote already exists
        if ($this->voteRepository->existsByPollIdAndName($poll_id, $name)) {
            throw new AlreadyExistsException();
        }

        // Insert new vote
        $choices = implode($choices);
        $token = $this->random(16);
        return $this->voteRepository->insert($poll_id, $name, $choices, $token);
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $comment
     * @return bool
     */
    public function addComment($poll_id, $name, $comment) {
        if ($this->commentRepository->exists($poll_id, $name, $comment)) {
            return true;
        }
        return $this->commentRepository->insert($poll_id, $name, $comment);
    }

    /**
     * @param Poll $poll
     * @return Poll
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function createPoll(Poll $poll) {
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

        // Insert poll + slots
        $this->pollRepository->beginTransaction();
        $this->pollRepository->insertPoll($poll);
        $this->slotRepository->insertSlots($poll->getId(), $poll->getChoices());
        $this->pollRepository->commit();

        $this->logger->info('CREATE_POLL' . 'id:' . $poll->getId() . ', title: ' . $poll->getTitle() . ', format:' . $poll->getFormat() . ', admin:' . $poll->getAdminName() . ', mail:' . $poll->getAdminMail());
        // $this->logService->log('CREATE_POLL', 'id:' . $poll_id . ', title: ' . $form->getTitle() . ', format:' . $form->getFormat() . ', admin:' . $form->getAdminName() . ', mail:' . $form->getAdminMail());

        return $poll;
    }

    public function existsById($poll_id)
    {
        return $this->pollRepository->existsById($poll_id);
    }

    public function findAllByAdminMail($mail) {
        return $this->pollRepository->findAllByAdminMail($mail);
    }

    function computeBestChoices($votes) {
        $result = ['y' => [0], 'inb' => [0]];
        foreach ($votes as $vote) {
            $choices = str_split($vote['choices']);
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

        return $result;
    }

    /**
     * @param array $slots
     * @return array
     */
    public function splitSlots($slots) {
        $splitted = [];
        foreach ($slots as $slot) {
            /** @var Slot $slot */
            $obj = new \stdClass();
            $obj->day = $slot->getTitle();
            $obj->moments = explode(',', $slot->getMoments());

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @param $slots array The slots to hash
     * @return string The hash
     */
    public function hashSlots($slots) {
        return md5(array_reduce($slots, function($carry, $item) {
            if ($item instanceof DateSlot) {
                /** @var DateSlot $item */
                return $carry . $item->getId() . '@' . $item->getMoments() . ';';
            }
            /** @var Slot $item */
            return $carry . $item->getId() . '@;';
        }));
    }

    /**
     * @param array $votes
     * @return array
     */
    public function splitVotes($votes) {
        $splitted = [];
        foreach ($votes as $vote) {
            $obj = new \stdClass();
            $obj->id = $vote['id'];
            $obj->name = $vote['name'];
            $obj->uniqId = $vote['uniqId'];
            $obj->choices = str_split($vote['choices']);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @return \DateTime The max timestamp allowed for expiry date
     */
    public function maxExpiryDate() {
        // TODO : inject proper config
        return (new \DateTime())->modify('+'. 60 .' day');
    }

    /**
     * @return \DateTime The min timestamp allowed for expiry date
     */
    public function minExpiryDate() {
        return (new \DateTime())->modify('+1 day');
    }

    /**
     * @param array $slots
     * @return array
     */
    public function sortSlorts(array &$slots) {
        uasort($slots, function (Slot $a, Slot $b) {
            return $a->getTitle() > $b->getTitle();
        });
        return $slots;
    }

    private function random($length) {
        return Token::getToken($length);
    }

    /**
     * This method checks if the hash send by the user is the same as the computed hash.
     *
     * @param $poll /stdClass The poll
     * @param $slots_hash string The hash sent by the user
     * @throws ConcurrentEditionException Thrown when hashes are differents
     */
    private function checkThatSlotsDidntChanged($poll, $slots_hash) {
        $slots = $this->allSlotsByPoll($poll);
        if ($slots_hash !== $this->hashSlots($slots)) {
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
    private function checkMaxVotes($user_choice, Poll $poll, $poll_id) {
        $votes = $this->allVotesByPollId($poll_id);
        if (count($votes) <= 0) {
            return;
        }
        $best_choices = $this->computeBestChoices($votes);
        foreach ($best_choices['y'] as $i => $nb_choice) {
            // if for this option we have reached maximum value and user wants to add itself too
            if ($poll->getValueMax() > 0 && $nb_choice >= $poll->getValueMax() && $user_choice[$i] === "2") {
                throw new ConcurrentVoteException();
            }
        }
    }
}
