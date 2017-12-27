<?php
namespace Framadate\Repositories;

use Framadate\FramaDB;
use Framadate\Utils;
use PDO;

class PollRepository extends AbstractRepository {

    function __construct(FramaDB $connect) {
        parent::__construct($connect);
    }

    public function insertPoll($poll_id, $admin_poll_id, $form) {
        $sql = 'INSERT INTO `' . Utils::table('poll') . '`
          (id, admin_id, title, description, admin_name, admin_mail, end_date, format, editable, receiveNewVotes, receiveNewComments, hidden, password_hash, results_publicly_visible)
          VALUES (?,?,?,?,?,?,FROM_UNIXTIME(?),?,?,?,?,?,?,?)';
        $prepared = $this->prepare($sql);
        $prepared->execute(array($poll_id, $admin_poll_id, $form->title, $form->description, $form->admin_name, $form->admin_mail, $form->end_date, $form->format, ($form->editable>=0 && $form->editable<=2) ? $form->editable : 0, $form->receiveNewVotes ? 1 : 0, $form->receiveNewComments ? 1 : 0, $form->hidden ? 1 : 0, $form->password_hash, $form->results_publicly_visible ? 1 : 0));
    }

    function findById($poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute(array($poll_id));
        $poll = $prepared->fetch();
        $prepared->closeCursor();

        return $poll;
    }

    public function findByAdminId($admin_poll_id) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE admin_id = ?');

        $prepared->execute(array($admin_poll_id));
        $poll = $prepared->fetch();
        $prepared->closeCursor();

        return $poll;
    }

    public function existsById($poll_id) {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute(array($poll_id));

        return $prepared->rowCount() > 0;
    }

    public function existsByAdminId($admin_poll_id) {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('poll') . '` WHERE admin_id = ?');

        $prepared->execute(array($admin_poll_id));

        return $prepared->rowCount() > 0;
    }

    function update($poll) {
        $prepared = $this->prepare('UPDATE `' . Utils::table('poll') . '` SET title=?, admin_name=?, admin_mail=?, description=?, end_date=?, active=?, editable=?, hidden=?, password_hash=?, results_publicly_visible=? WHERE id = ?');

        return $prepared->execute([$poll->title, $poll->admin_name, $poll->admin_mail, $poll->description, $poll->end_date, $poll->active, ($poll->editable>=0 && $poll->editable<=2) ? $poll->editable  : 0, $poll->hidden ? 1 : 0, $poll->password_hash, $poll->results_publicly_visible ? 1 : 0, $poll->id]);
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
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
     * @param int $start The number of first entry to select
     * @param int $limit The number of entries to find
     * @return array The found polls
     */
    public function findAll($search, $start, $limit) {
        // Polls
        $prepared = $this->prepare('
SELECT p.*,
       (SELECT count(1) FROM `' . Utils::table('vote') . '` v WHERE p.id=v.poll_id) votes
  FROM `' . Utils::table('poll') . '` p
 WHERE (:id = "" OR p.id LIKE :id)
   AND (:title = "" OR p.title LIKE :title)
   AND (:name = "" OR p.admin_name LIKE :name)
   AND (:mail = "" OR p.admin_mail LIKE :mail)
 ORDER BY p.title ASC
 LIMIT :start, :limit
 ');

        $poll = $search['poll'] . '%';
        $title = '%' . $search['title'] . '%';
        $name = '%' . $search['name'] . '%';
        $mail = '%' . $search['mail'] . '%';
        $prepared->bindParam(':id', $poll, PDO::PARAM_STR);
        $prepared->bindParam(':title', $title, PDO::PARAM_STR);
        $prepared->bindParam(':name', $name, PDO::PARAM_STR);
        $prepared->bindParam(':mail', $mail, PDO::PARAM_STR);
        $prepared->bindParam(':start', $start, PDO::PARAM_INT);
        $prepared->bindParam(':limit', $limit, PDO::PARAM_INT);
        $prepared->execute();

        return $prepared->fetchAll();
    }

    /**
     * Find all polls that are created with the given admin mail.
     *
     * @param string $mail Email address of the poll admin
     * @return array The list of matching polls
     */
    public function findAllByAdminMail($mail) {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE admin_mail = :admin_mail');
        $prepared->execute(array('admin_mail' => $mail));

        return $prepared->fetchAll();
    }

    /**
     * Get the total number of polls in databse.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @return int The number of polls
     */
    public function count($search = null) {
        // Total count
        $prepared = $this->prepare('
SELECT count(1) nb
  FROM `' . Utils::table('poll') . '` p
 WHERE (:id = "" OR p.id LIKE :id)
   AND (:title = "" OR p.title LIKE :title)
   AND (:name = "" OR p.admin_name LIKE :name)
 ORDER BY p.title ASC');

        $poll = $search == null ? '' : $search['poll'] . '%';
        $title = $search == null ? '' : '%' . $search['title'] . '%';
        $name = $search == null ? '' : '%' . $search['name'] . '%';
        $prepared->bindParam(':id', $poll, PDO::PARAM_STR);
        $prepared->bindParam(':title', $title, PDO::PARAM_STR);
        $prepared->bindParam(':name', $name, PDO::PARAM_STR);

        $prepared->execute();
        $count = $prepared->fetch();

        /*echo '---';
        print_r($count);
        echo '---';
        exit;*/

        return $count->nb;
    }

}
