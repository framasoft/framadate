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

class PollService {

    private $connect;
    private $logService;

    function __construct(FramaDB $connect, LogService $logService) {
        $this->connect = $connect;
        $this->logService = $logService;
    }

    /**
     * Find a poll from its ID.
     *
     * @param $poll_id int The ID of the poll
     * @return \stdClass|null The found poll, or null
     */
    function findById($poll_id) {
        if (preg_match('/^[\w\d]{16}$/i', $poll_id)) {
            return $this->connect->findPollById($poll_id);
        }

        return null;
    }

    function allCommentsByPollId($poll_id) {
        return $this->connect->allCommentsByPollId($poll_id);
    }

    function allUserVotesByPollId($poll_id) {
        return $this->connect->allUserVotesByPollId($poll_id);
    }

    function allSlotsByPollId($poll_id) {
        return $this->connect->allSlotsByPollId($poll_id);
    }

    public function updateVote($poll_id, $vote_id, $choices) {
        $choices = implode($choices);

        return $this->connect->updateVote($poll_id, $vote_id, $choices);
    }

    function addVote($poll_id, $name, $choices) {
        $choices = implode($choices);

        return $this->connect->insertVote($poll_id, $name, $choices);
    }

    function addComment($poll_id, $name, $comment) {
        // TODO Check if there is no duplicate before to add a new comment
        return $this->connect->insertComment($poll_id, $name, $comment);
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
            $obj->choices = str_split($vote->choices);

            $splitted[] = $obj;
        }

        return $splitted;
    }

    /**
     * @param Form $form
     * @return string
     */
    function createPoll(Form $form) {

        // Generate poll IDs
        $poll_id = $this->random(16);
        $admin_poll_id = $poll_id . $this->random(8);

        // Insert poll + slots
        $this->connect->beginTransaction();

        // TODO Extract this to FramaDB (or repository layer)
        $sql = 'INSERT INTO ' . Utils::table('poll') . '
          (id, admin_id, title, description, admin_name, admin_mail, end_date, format, editable, receiveNewVotes)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?)';
        $prepared = $this->connect->prepare($sql);
        $prepared->execute(array($poll_id, $admin_poll_id, $form->title, $form->description, $form->admin_name, $form->admin_mail, $form->end_date, $form->format, $form->editable, $form->receiveNewVotes));

        $prepared = $this->connect->prepare('INSERT INTO ' . Utils::table('slot') . ' (poll_id, title, moments) VALUES (?, ?, ?)');

        foreach ($form->getChoices() as $choice) {

            // We prepared the slots (joined by comas)
            $joinedSlots = '';
            $first = true;
            foreach ($choice->getSlots() as $slot) {
                if ($first) {
                    $joinedSlots = $slot;
                    $first = false;
                } else {
                    $joinedSlots .= ',' . $slot;
                }
            }

            // We execute the insertion
            if (empty($joinedSlots)) {
                $prepared->execute(array($poll_id, $choice->getName(), null));
            } else {
                $prepared->execute(array($poll_id, $choice->getName(), $joinedSlots));
            }

        }

        $this->connect->commit();

        $this->logService->log('CREATE_POLL', 'id:' . $poll_id . ', title: ' . $form->title . ', format:' . $form->format . ', admin:' . $form->admin_name . ', mail:' . $form->admin_mail);


        return [$poll_id, $admin_poll_id];
    }

    private function random($car) {
        // TODO Better random ?
        $string = '';
        $chaine = 'abcdefghijklmnopqrstuvwxyz123456789';
        mt_srand();
        for ($i = 0; $i < $car; $i++) {
            $string .= $chaine[mt_rand() % strlen($chaine)];
        }

        return $string;
    }
}
