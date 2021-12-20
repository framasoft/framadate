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

use DateInterval;
use DateTime;
use Exception;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Exception\PollNotFoundException;
use Framadate\Form;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Security\Token;
use stdClass;

class PollService {
    private $logService;

    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    public function __construct(LogService $logService) {
        $this->logService = $logService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    /**
     * Find a poll from its ID.
     *
     * @param string $poll_id The ID of the poll
     * @return stdClass|null The found poll, or null
     */
    public function findById(string $poll_id) {
        if (preg_match(POLL_REGEX, $poll_id)) {
            return $this->pollRepository->findById($poll_id);
        }

        return null;
    }

    public function findByAdminId(string $admin_poll_id) {
        if (preg_match(ADMIN_POLL_REGEX, $admin_poll_id)) {
            return $this->pollRepository->findByAdminId($admin_poll_id);
        }

        return null;
    }

    public function allCommentsByPollId(string $poll_id) {
        return $this->commentRepository->findAllByPollId($poll_id);
    }

    public function allVotesByPollId(string $poll_id) {
        return $this->voteRepository->allUserVotesByPollId($poll_id);
    }

    public function allSlotsByPoll(stdClass $poll) {
        $slots = $this->slotRepository->listByPollId($poll->id);
        if ($poll->format === 'D') {
            $this->sortSlorts($slots);
        }
        return $slots;
    }

    /**
     * @param string $poll_id
     * @param int $vote_id
     * @param string $name
     * @param array $choices
     * @param string $slots_hash
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @return bool
     */
    public function updateVote(string $poll_id, int $vote_id, string $name, array $choices, string $slots_hash): bool
    {
        $this->checkVoteConstraints($choices, $poll_id, $slots_hash, $name, $vote_id);

        // Update vote
        return $this->voteRepository->update($poll_id, $vote_id, $name, implode($choices));
    }

    /**
     * @param string $poll_id
     * @param string $name
     * @param array $choices
     * @param string $slots_hash
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @throws PollNotFoundException
     * @throws AlreadyExistsException
     * @return stdClass
     */
    public function addVote(string $poll_id, string $name, array $choices, string $slots_hash): stdClass
    {
        $this->checkVoteConstraints($choices, $poll_id, $slots_hash, $name);

        // Insert new vote
        return $this->voteRepository->insert($poll_id, $name, implode($choices), $this->random(16));
    }

    public function addComment($poll_id, $name, $comment): bool
    {
        if ($this->commentRepository->exists($poll_id, $name, $comment)) {
            return true;
        }

        return $this->commentRepository->insert($poll_id, $name, $comment);
    }

    /**
     * @param Form $form
     * @return array
     */
    public function createPoll(Form $form): array
    {
        // Generate poll IDs, loop while poll ID already exists

        if (empty($form->id)) { // User want us to generate an id for him
            do {
                $poll_id = $this->random(16);
            } while ($this->pollRepository->existsById($poll_id));
            $admin_poll_id = $poll_id . $this->random(8);
        } else { // User have choosen the poll id
            $poll_id = $form->id;
            do {
                $admin_poll_id = $this->random(24);
            } while ($this->pollRepository->existsByAdminId($admin_poll_id));
        }

        // Insert poll + slots
        $this->pollRepository->beginTransaction();
        $this->pollRepository->insertPoll($poll_id, $admin_poll_id, $form);
        $this->slotRepository->insertSlots($poll_id, $form->getChoices());
        $this->pollRepository->commit();

        $this->logService->log('CREATE_POLL', 'id:' . $poll_id . ', title: ' . $form->title . ', format:' . $form->format . ', admin:' . $form->admin_name . ', mail:' . $form->admin_mail);

        return [$poll_id, $admin_poll_id];
    }

    public function findAllByAdminMail($mail): array
    {
        return $this->pollRepository->findAllByAdminMail($mail);
    }

    /**
     * @param array $votes
     * @param stdClass $poll
     * @return array
     */
    public function computeBestChoices(array $votes, $poll): array
    {
        if (0 === count($votes)) {
           return $this->computeEmptyBestChoices($poll);
        }
        $result = ['y' => [], 'inb' => []];

        // if there are votes
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

        return $result;
    }

    public function splitSlots($slots): array
    {
        $splitted = [];
        foreach ($slots as $slot) {
            $obj = new stdClass();
            $obj->day = $slot->title;
            $obj->moments = explode(',', $slot->moments);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @param $slots array The slots to hash
     * @return string The hash
     */
    public function hashSlots(array $slots): string
    {
        return md5(array_reduce($slots, static function($carry, $item) {
            return $carry . $item->id . '@' . $item->moments . ';';
        }));
    }

    public function splitVotes(array $votes): array
    {
        $splitted = [];
        foreach ($votes as $vote) {
            $obj = new stdClass();
            $obj->id = $vote->id;
            $obj->name = $vote->name;
            $obj->uniqId = $vote->uniqId;
            $obj->choices = str_split($vote->choices);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @throws Exception
     * @return DateTime The max date allowed for expiry date
     */
    public function maxExpiryDate(): DateTime {
        global $config;
        return (new DateTime())->add(new DateInterval('P' . $config['default_poll_duration'] . 'D'));
    }

    /**
     * @return DateTime The min date allowed for expiry date
     */
    public function minExpiryDate(): DateTime
    {
        return (new DateTime())->add(new DateInterval('P1D'));
    }

    /**
     * @return mixed
     */
    public function sortSlorts(array &$slots): array {
        uasort($slots, static function ($a, $b) {
            if ($a->title === $b->title) {
                return 0;
            }
            return $a->title > $b->title ? 1 : -1;
        });
        return $slots;
    }

    /**
     * @param stdClass $poll
     * @return array
     */
    private function computeEmptyBestChoices($poll): array
    {
        $result = ['y' => [], 'inb' => []];
        // if there is no votes, calculates the number of slot

        $slots = $this->allSlotsByPoll($poll);

        if ($poll->format === 'A') {
            // poll format classic

            for ($i = 0, $iMax = count($slots); $i < $iMax; $i++) {
                $result['y'][] = 0;
                $result['inb'][] = 0;
            }
        } else {
            // poll format date

            $slots = $this->splitSlots($slots);

            foreach ($slots as $slot) {
                for ($i = 0, $iMax = count($slot->moments); $i < $iMax; $i++) {
                    $result['y'][] = 0;
                    $result['inb'][] = 0;
                }
            }
        }
        return $result;
    }

    private function random(int $length): string
    {
        return Token::getToken($length);
    }

    /**
     * @param array $choices
     * @param string $poll_id
     * @param string $slots_hash
     * @param string $name
     * @param bool|int $vote_id
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @throws PollNotFoundException
     */
    private function checkVoteConstraints(array $choices, string $poll_id, string $slots_hash, string $name, $vote_id = false): void
    {
        // Check if vote already exists with the same name
        if (false === $vote_id) {
        	$exists = $this->voteRepository->existsByPollIdAndName($poll_id, $name);
        } else {
        	$exists = $this->voteRepository->existsByPollIdAndNameAndVoteId($poll_id, $name, $vote_id);
        }

        if ($exists) {
            throw new AlreadyExistsException();
        }

        $poll = $this->findById($poll_id);

        if (!$poll) {
            throw new PollNotFoundException();
        }

        // Check that no-one voted in the meantime and it conflicts the maximum votes constraint
        $this->checkMaxVotes($choices, $poll, $poll_id);

        // Check if slots are still the same
        $this->checkThatSlotsDidntChanged($poll, $slots_hash);
    }

    /**
     * This method checks if the hash send by the user is the same as the computed hash.
     *
     * @param $poll /stdClass The poll
     * @param $slots_hash string The hash sent by the user
     * @throws ConcurrentEditionException Thrown when hashes are differents
     */
    private function checkThatSlotsDidntChanged(stdClass $poll, string $slots_hash): void
    {
        $slots = $this->allSlotsByPoll($poll);
        if ($slots_hash !== $this->hashSlots($slots)) {
            throw new ConcurrentEditionException();
        }
    }

    /**
     * This method checks if the votes doesn't conflicts the maximum votes constraint
     *
     * @param $user_choice
     * @param stdClass $poll
     * @param string $poll_id
     * @throws ConcurrentVoteException
     */
    private function checkMaxVotes(array $user_choice, $poll, string $poll_id): void
    {
        $votes = $this->allVotesByPollId($poll_id);
        if (count($votes) <= 0) {
            return;
        }
        $best_choices = $this->computeBestChoices($votes, $poll);
        foreach ($best_choices['y'] as $i => $nb_choice) {
            // if for this option we have reached maximum value and user wants to add itself too
            if ($poll->ValueMax !== null && $nb_choice >= $poll->ValueMax && $user_choice[$i] === "2") {
                throw new ConcurrentVoteException();
            }
        }
    }
}
