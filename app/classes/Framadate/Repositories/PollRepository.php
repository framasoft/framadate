<?php
namespace Framadate\Repositories;

use Framadate\Utils;

class PollRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

    public function insertPoll($poll_id, $admin_poll_id, $form) {
        $sql = 'INSERT INTO `' . Utils::table('poll') . '`
          (id, admin_id, title, description, admin_name, admin_mail, end_date, format, editable, receiveNewVotes, receiveNewComments)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?,?)';
        $prepared = $this->prepare($sql);
        $prepared->execute(array($poll_id, $admin_poll_id, $form->title, $form->description, $form->admin_name, $form->admin_mail, $form->end_date, $form->format, $form->editable, $form->receiveNewVotes, $form->receiveNewComments));
    }

    function findById($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute(array($poll_id));
        $poll = $prepared->fetch();
        $prepared->closeCursor();

        return $poll;
    }

    public function existsById($poll_id) {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute(array($poll_id));

        return $prepared->rowCount() > 0;
    }

    function update($poll) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('poll') . '` SET title=?, admin_name=?, admin_mail=?, description=?, end_date=?, active=?, editable=? WHERE id = ?');

        return $prepared->execute([$poll->title, $poll->admin_name, $poll->admin_mail, $poll->description, $poll->end_date, $poll->active, $poll->editable, $poll->id]);
    }

}
