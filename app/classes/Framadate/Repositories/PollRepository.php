<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;

class PollRepository {

    /**
     * @var FramaDB
     */
    private $connect;

    /**
     * PollRepository constructor.
     * @param FramaDB $connect
     */
    function __construct($connect) {
        $this->connect = $connect;
    }

    public function beginTransaction() {
        $this->connect->beginTransaction();
    }

    public function commit() {
        $this->connect->commit();
    }

    public function insertPoll($poll_id, $admin_poll_id, $form) {
        $sql = 'INSERT INTO `' . Utils::table('poll') . '`
          (id, admin_id, title, description, admin_name, admin_mail, end_date, format, editable, receiveNewVotes, receiveNewComments)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?,?)';
        $prepared = $this->connect->prepare($sql);
        $prepared->execute(array($poll_id, $admin_poll_id, $form->title, $form->description, $form->admin_name, $form->admin_mail, $form->end_date, $form->format, $form->editable, $form->receiveNewVotes, $form->receiveNewComments));
    }

    /**
     * @param int $poll_id
     * @param array $choices
     */
    public function insertSlots($poll_id, $choices) {
        $prepared = $this->connect->prepare('INSERT INTO ' . Utils::table('slot') . ' (poll_id, title, moments) VALUES (?, ?, ?)');

        foreach ($choices as $choice) {

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
    }

}
