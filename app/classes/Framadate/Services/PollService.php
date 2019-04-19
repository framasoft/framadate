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
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Exception;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Form;
use Framadate\Repositories\RepositoryFactory;
use Framadate\Security\Token;
use RuntimeException;
use stdClass;

class PollService {
    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;
    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var SessionService
     */
    private $sessionService;
    /**
     * @var PurgeService
     */
    private $purgeService;

    /**
     * @var LogService
     */
    private $logService;

    public function __construct(LogService $logService, NotificationService $notificationService, SessionService $sessionService, PurgeService $purgeService) {
        $this->logService = $logService;
        $this->notificationService = $notificationService;
        $this->sessionService = $sessionService;
        $this->purgeService = $purgeService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    /**
     * Find a poll from its ID.
     *
     * @param $poll_id int The ID of the poll
     * @return stdClass|null The found poll, or null
     */
    function findById($poll_id) {
        try {
            if (preg_match(POLL_REGEX, $poll_id)) {
                return $this->pollRepository->findById($poll_id);
            }
        } catch (DBALException $e) {
            $this->logService->log('ERROR', 'Database error : ' . $e->getMessage());
        }

        return null;
    }

    /**
     * @param $admin_poll_id
     * @return mixed|null
     */
    public function findByAdminId($admin_poll_id) {
        try {
            if (preg_match(ADMIN_POLL_REGEX, $admin_poll_id)) {
                return $this->pollRepository->findByAdminId($admin_poll_id);
            }
        } catch (DBALException $e) {
            $this->logService->log('ERROR', 'Database error : ' . $e->getMessage());
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
            $this->logService->log('error', $e->getMessage());
            return null;
        }
    }

    function allVotesByPollId($poll_id) {
        return $this->voteRepository->allUserVotesByPollId($poll_id);
    }

    function allSlotsByPoll($poll) {
        $slots = $this->slotRepository->listByPollId($poll->id);
        if ($poll->format === 'D') {
            $this->sortSlorts($slots);
        }
        return $slots;
    }

    /**
     * @param $poll_id
     * @param $vote_id
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @param string $mail
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @return bool
     */
    public function updateVote($poll_id, $vote_id, $name, $choices, $slots_hash, $mail) {
        $this->checkVoteConstraints($choices, $poll_id, $slots_hash, $name, $vote_id);

        // Update vote
        $choices = implode($choices);
        return $this->voteRepository->update($poll_id, $vote_id, $name, $choices, $mail);
    }

    /**
     * @param $poll_id
     * @param $name
     * @param $choices
     * @param $slots_hash
     * @param string $mail
     * @throws AlreadyExistsException
     * @throws ConcurrentEditionException
     * @throws ConcurrentVoteException
     * @return stdClass
     */
    function addVote($poll_id, $name, $choices, $slots_hash, $mail) {
        $this->checkVoteConstraints($choices, $poll_id, $slots_hash, $name);

        // Insert new vote
        $choices = implode($choices);
        $token = $this->random(16);
        return $this->voteRepository->insert($poll_id, $name, $choices, $token, $mail);
    }

    function addComment($poll_id, $name, $comment) {
        if ($this->commentRepository->exists($poll_id, $name, $comment)) {
            return true;
        }

        return $this->commentRepository->insert($poll_id, $name, $comment);
    }

    /**
     * Main entry point for poll creation. This action does all the following:
     * - Validate input data
     * - Create the poll in database
     * - Send emails
     * - Cleanups post creation (session and purges)
     *
     * @param Form $form
     * @param string $end_date
     * @return string|null returns the admin_id if the poll was created
     */
    function doPollCreation(Form $form, $end_date) {
        // Min/Max archive date
        $min_expiry_time = $this->minExpiryDate();
        $max_expiry_time = $this->maxExpiryDate();

        if (!empty($end_date)) {
            $registredate = explode('/', $end_date);

            if (is_array($registredate) && count($registredate) === 3) {
                $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

                if ($time < $min_expiry_time) {
                    $form->end_date = $min_expiry_time;
                } elseif ($max_expiry_time < $time) {
                    $form->end_date = $max_expiry_time;
                } else {
                    $form->end_date = $time;
                }
            }
        }

        if (empty($form->end_date)) {
            // By default, expiration date is 6 months after last day
            $form->end_date = $max_expiry_time;
        }

        // Insert poll in database
        list($poll_id, $admin_poll_id) = $this->createPoll($form);

        // Send confirmation by mail if enabled
        if (!is_null($ids)) {
            // Everything went well
            $this->notificationService->sendPollCreationMails($form->admin_mail, $form->admin_name, $form->title, $poll_id, $admin_poll_id);

            // Clean Form data in session
            $this->sessionService->removeAll('form');
            // Creation message
            $this->sessionService->set("Framadate", "messagePollCreated", TRUE);
            // Delete old polls
            $this->purgeService->repeatedCleanings();
        } else {
            // Add an error in the form
            $form->errors = [
                __('Error', 'GenericErrorPollCreation')
            ];
            // TODO: change this to use sessionService function
            $_SESSION['form'] = serialize($form);
        }

        return $admin_poll_id;
    }

    /**
     * @param Form $form
     * @throws ConnectionException
     * @return array
     */
    public function createPoll(Form $form) {
        // Generate poll IDs, loop while poll ID already exists

        $this->pollRepository->beginTransaction();
        try {
            if (empty($form->id)) { // User want us to generate an id for him
                do {
                    $poll_id = $this->random(16);
                } while ($this->pollRepository->existsById($poll_id));
                $admin_poll_id = $poll_id . $this->random(8);
            } else { // User have chosen the poll id
                $poll_id = $form->id;
                do {
                    $admin_poll_id = $this->random(24);
                } while ($this->pollRepository->existsByAdminId($admin_poll_id));
            }

            // Insert poll + slots
            $this->pollRepository->insertPoll($poll_id, $admin_poll_id, $form);
            $this->slotRepository->insertSlots($poll_id, $form->getChoices());
            $this->pollRepository->commit();

            $this->logService->log(
                'CREATE_POLL',
                'id:' . $poll_id . ', title: ' . $form->title . ', format:' . $form->format . ', admin:' . $form->admin_name . ', mail:' . $form->admin_mail
            );

            return [$poll_id, $admin_poll_id];
        } catch (DBALException $e) {
            $this->pollRepository->rollback();
            $this->logService->log('ERROR', "Poll couldn't be saved : " . $e->getMessage());
            return null;
        }
    }

    public function findAllByAdminMail($mail) {
        return $this->pollRepository->findAllByAdminMail($mail);
    }

    /**
     * @param array $votes
     * @param stdClass $poll
     * @return array
     */
    public function computeBestChoices($votes, $poll) {
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

    function splitSlots($slots) {
        $splitted = [];
        foreach ($slots as $slot) {
            $obj = new stdClass();
            $obj->day = (new DateTime())->setTimestamp((int) $slot->title);
            $obj->moments = explode(',', $slot->moments);

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
            return $carry . $item->id . '@' . $item->moments . ';';
        }));
    }

    function splitVotes($votes) {
        $splitted = [];
        foreach ($votes as $vote) {
            $obj = new stdClass();
            $obj->id = $vote->id;
            $obj->name = $vote->name;
            $obj->uniqId = $vote->uniqId;
            $obj->choices = str_split($vote->choices);
	    $obj->mail = $vote->mail;

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @throws Exception
     * @return DateTime The max timestamp allowed for expiry date
     */
    public function maxExpiryDate() {
        try {
            global $config;
            $default_poll_duration = isset($config['default_poll_duration']) ? $config['default_poll_duration'] : 60;
            return (new DateTime())->add(new DateInterval('P' . $default_poll_duration . 'D'));
        } catch (Exception $e) {
            throw new RuntimeException('Configuration Exception');
        }
    }

    /**
     * @throws Exception
     * @return DateTime The min timestamp allowed for expiry date
     */
    public function minExpiryDate() {
        return (new DateTime())->add(new DateInterval('P1D'));
    }

    /**
     * @return mixed
     */
    public function sortSlorts(&$slots) {
        uasort($slots, function ($a, $b) {
            return $a->title > $b->title;
        });
        return $slots;
    }

    /**
     * @param stdClass $poll
     * @return array
     */
    private function computeEmptyBestChoices($poll)
    {
        $result = ['y' => [], 'inb' => []];
        // if there is no votes, calculates the number of slot

        $slots = $this->allSlotsByPoll($poll);

        if ($poll->format === 'A') {
            // poll format classic

            for ($i = 0; $i < count($slots); $i++) {
                $result['y'][] = 0;
                $result['inb'][] = 0;
            }
        } else {
            // poll format date

            $slots = $this->splitSlots($slots);

            foreach ($slots as $slot) {
                for ($i = 0; $i < count($slot->moments); $i++) {
                    $result['y'][] = 0;
                    $result['inb'][] = 0;
                }
            }
        }
        return $result;
    }

    private function random($length) {
        return Token::getToken($length);
    }

    /**
     * @param $choices
     * @param $poll_id
     * @param $slots_hash
     * @param $name
     * @param string $vote_id
     * @throws AlreadyExistsException
     * @throws ConcurrentVoteException
     * @throws ConcurrentEditionException
     */
    private function checkVoteConstraints($choices, $poll_id, $slots_hash, $name, $vote_id = FALSE) {
        // Check if vote already exists with the same name
        if (FALSE === $vote_id) {
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
     * @param stdClass $poll
     * @param string $poll_id
     * @throws ConcurrentVoteException
     */
    private function checkMaxVotes($user_choice, $poll, $poll_id) {
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
