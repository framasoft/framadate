<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;
use PDO;

class PollRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

    public function insertPoll($poll_id, $admin_poll_id, $form, $password_hash, $results_publicly_visible) {
        $sql = 'INSERT INTO `' . Utils::table('poll') . '`
          (id, admin_id, title, description, admin_name, admin_mail, end_date, format, editable, receiveNewVotes, receiveNewComments, hidden, password_hash, results_publicly_visible)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?,?,?,?,?)';
        $prepared = $this->prepare($sql);
        $prepared->execute(array($poll_id, $admin_poll_id, $form->title, $form->description, $form->admin_name, $form->admin_mail, $form->end_date, $form->format, $form->editable, $form->receiveNewVotes, $form->receiveNewComments, $form->hidden, $password_hash, $results_publicly_visible));
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
        $prepared = $this->prepare('UPDATE `' . Utils::table('poll') . '` SET title=?, admin_name=?, admin_mail=?, description=?, end_date=?, active=?, editable=?, hidden=? WHERE id = ?');

        return $prepared->execute([$poll->title, $poll->admin_name, $poll->admin_mail, $poll->description, $poll->end_date, $poll->active, $poll->editable, $poll->hidden, $poll->id]);
    }

    function deleteById($poll_id) {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('poll') . '` WHERE id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Find old polls. Limit: 20.
     *
     * @return array Array of old polls
     */
    public function findOldPolls() {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE DATE_ADD(`end_date`, INTERVAL ' . PURGE_DELAY . ' DAY) < NOW() AND `end_date` != 0 LIMIT 20');
        $prepared->execute([]);

        return $prepared->fetchAll();
    }

    /**
     * Search polls in databse.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @return array The found polls
     */
    public function findAll($search) {
        // Polls
        $prepared = $this->prepare('
SELECT p.*,
       (SELECT count(1) FROM `' . Utils::table('vote') . '` v WHERE p.id=v.poll_id) votes
  FROM `' . Utils::table('poll') . '` p
 WHERE (:id = "" OR p.id LIKE :id)
   AND (:title = "" OR p.title LIKE :title)
   AND (:name = "" OR p.admin_name LIKE :name)
 ORDER BY p.title ASC
 ');

        $poll = $search['poll'] . '%';
        $title = '%' . $search['title'] . '%';
        $name = '%' . $search['name'] . '%';
        $prepared->bindParam(':id', $poll, PDO::PARAM_STR);
        $prepared->bindParam(':title', $title, PDO::PARAM_STR);
        $prepared->bindParam(':name', $name, PDO::PARAM_STR);
        $prepared->execute();

        return $prepared->fetchAll();
    }

    /**
     * Get the total number of polls in databse.
     *
     * @return int The number of polls
     */
    public function count() {
        // Total count
        $stmt = $this->query('SELECT count(1) nb FROM `' . Utils::table('poll') . '`');
        $count = $stmt->fetch();
        $stmt->closeCursor();

        return $count->nb;
    }

}
