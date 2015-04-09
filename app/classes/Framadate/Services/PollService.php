<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
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

use Framadate\Form;
use Framadate\FramaDB;
use Framadate\Utils;
use Framadate\Security\Token;
use Framadate\Repositories\RepositoryFactory;

class PollService {

    private $connect;
    private $logService;

    private $pollRepository;
    private $slotRepository;
    private $voteRepository;
    private $commentRepository;

    function __construct(FramaDB $connect, LogService $logService) {
        $this->connect = $connect;
        $this->logService = $logService;
        $this->pollRepository = RepositoryFactory::pollRepository();
        $this->slotRepository = RepositoryFactory::slotRepository();
        $this->voteRepository = RepositoryFactory::voteRepository();
        $this->commentRepository = RepositoryFactory::commentRepository();
    }

    /**
     * Find a poll from its ID.
     *
     * @param $poll_id int The ID of the poll
     * @return \stdClass|null The found poll, or null
     */
    function findById($poll_id) {
        if (preg_match('/^[\w\d]{16}$/i', $poll_id)) {
            return $this->pollRepository->findById($poll_id);
        }

        return null;
    }

    function allCommentsByPollId($poll_id) {
        return $this->commentRepository->findAllByPollId($poll_id);
    }

    function allVotesByPollId($poll_id) {
        return $this->voteRepository->allUserVotesByPollId($poll_id);
    }

    function allSlotsByPollId($poll_id) {
        return $this->slotRepository->listByPollId($poll_id);
    }

    public function updateVote($poll_id, $vote_id, $name, $choices) {
        $choices = implode($choices);

        return $this->voteRepository->update($poll_id, $vote_id, $name, $choices);
    }

    function addVote($poll_id, $name, $choices) {
        $choices = implode($choices);
        $token = $this->random(16);
        return $this->voteRepository->insert($poll_id, $name, $choices, $token);
    }

    function addComment($poll_id, $name, $comment) {
        if ($this->commentRepository->exists($poll_id, $name, $comment)) {
            return true;
        } else {
            return $this->commentRepository->insert($poll_id, $name, $comment);
        }
    }

    /**
     * @param Form $form
     * @return string
     */
    function createPoll(Form $form) {

        // Generate poll IDs, loop while poll ID already exists
        do {
            $poll_id = $this->random(16);
        } while ($this->pollRepository->existsById($poll_id));
        $admin_poll_id = $poll_id . $this->random(8);

        // Insert poll + slots
        $this->pollRepository->beginTransaction();
        $this->pollRepository->insertPoll($poll_id, $admin_poll_id, $form);
        $this->slotRepository->insertSlots($poll_id, $form->getChoices());
        $this->pollRepository->commit();

        $this->logService->log('CREATE_POLL', 'id:' . $poll_id . ', title: ' . $form->title . ', format:' . $form->format . ', admin:' . $form->admin_name . ', mail:' . $form->admin_mail);

        return array($poll_id, $admin_poll_id);
    }

    function computeBestChoices($votes) {
        $result = [];
        foreach ($votes as $vote) {
            $choices = str_split($vote->choices);
            foreach ($choices as $i => $choice) {
                if (empty($result[$i])) {
                    $result[$i] = 0;
                }
                if ($choice == 2) {
                    $result[$i]++;
                }
            }
        }

        return $result;
    }

    function splitSlots($slots) {
        $splitted = array();
        foreach ($slots as $slot) {
            $obj = new \stdClass();
            $obj->day = $slot->title;
            $obj->moments = explode(',', $slot->moments);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    function splitVotes($votes) {
        $splitted = array();
        foreach ($votes as $vote) {
            $obj = new \stdClass();
            $obj->id = $vote->id;
            $obj->name = $vote->name;
            $obj->uniqId = $vote->uniqId;
            $obj->choices = str_split($vote->choices);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    private function random($length) {
        return Token::getToken($length);
    }

}
