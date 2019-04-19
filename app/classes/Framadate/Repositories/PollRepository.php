<?php
namespace Framadate\Repositories;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Type;
use Exception;
use Framadate\Form;
use Framadate\Utils;
use PDO;

class PollRepository extends AbstractRepository {
    /**
     * @param $poll_id
     * @param $admin_poll_id
     * @param Form $form
     * @throws Exception
     */
    public function insertPoll($poll_id, $admin_poll_id, Form $form)
    {
        $this->connect->insert(Utils::table('poll'), [
            'id' => $poll_id,
            'admin_id' => $admin_poll_id,
            'title' => $form->title,
            'description' => $form->description,
            'admin_name' => $form->admin_name,
            'admin_mail' => $form->admin_mail,
            'end_date' => $form->end_date->format('Y-m-d H:i:s'),
            'format' => $form->format,
            'editable' => ($form->editable>=0 && $form->editable<=2) ? $form->editable : 0,
            'receiveNewVotes' => $form->receiveNewVotes ? 1 : 0,
            'receiveNewComments' => $form->receiveNewComments ? 1 : 0,
            'hidden' => $form->hidden ? 1 : 0,
            'password_hash' => $form->password_hash,
            'results_publicly_visible' => $form->results_publicly_visible ? 1 : 0,
            'ValueMax' => $form->ValueMax,
            'collect_users_mail' => ($form->collect_users_mail >= 0 && $form->collect_users_mail <= 3) ? $form->collect_users_mail : 0,
        ]);
    }

    /**
     * @param $poll_id
     * @throws DBALException
     * @return mixed
     */
    public function findById($poll_id)
    {
        $prepared = $this->connect->executeQuery('SELECT * FROM ' . Utils::table('poll') . ' WHERE id = ?', [$poll_id]);

        $poll = $prepared->fetch();
        $prepared->closeCursor();

        /**
         * Hack to make date a proper DateTime
         */
        $poll->creation_date = Type::getType(Type::DATETIME)->convertToPhpValue($poll->creation_date, $this->connect->getDatabasePlatform());
        $poll->end_date = Type::getType(Type::DATETIME)->convertToPhpValue($poll->end_date, $this->connect->getDatabasePlatform());

        return $poll;
    }

    /**
     * @param $admin_poll_id
     * @throws DBALException
     * @return mixed
     */
    public function findByAdminId($admin_poll_id) {
        $prepared = $this->connect->executeQuery('SELECT * FROM ' . Utils::table('poll') . ' WHERE admin_id = ?', [$admin_poll_id]);

        $poll = $prepared->fetch();
        $prepared->closeCursor();

        $poll->creation_date = Type::getType(Type::DATETIME)->convertToPhpValue($poll->creation_date, $this->connect->getDatabasePlatform());
        $poll->end_date = Type::getType(Type::DATETIME)->convertToPhpValue($poll->end_date, $this->connect->getDatabasePlatform());

        return $poll;
    }

    /**
     * @param $poll_id
     * @throws DBALException
     * @return bool
     */
    public function existsById($poll_id) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('poll') . ' WHERE id = ?');

        $prepared->execute([$poll_id]);

        return $prepared->rowCount() > 0;
    }

    /**
     * @param $admin_poll_id
     * @throws DBALException
     * @return bool
     */
    public function existsByAdminId($admin_poll_id) {
        $prepared = $this->prepare('SELECT 1 FROM ' . Utils::table('poll') . ' WHERE admin_id = ?');

        $prepared->execute([$admin_poll_id]);

        return $prepared->rowCount() > 0;
    }

    /**
     * @param $poll
     * @return bool
     */
    public function update($poll)
    {
        return $this->connect->update(Utils::table('poll'), [
            'title' => $poll->title,
            'admin_name' => $poll->admin_name,
            'admin_mail' => $poll->admin_mail,
            'description' => $poll->description,
            'end_date' => $poll->end_date->format('Y-m-d H:i:s'), # TODO : Harmonize dates between here and insert
            'active' => $poll->active,
            'editable' => $poll->editable >= 0 && $poll->editable <= 2 ? $poll->editable : 0,
            'hidden' => $poll->hidden ? 1 : 0,
            'password_hash' => $poll->password_hash,
            'results_publicly_visible' => $poll->results_publicly_visible ? 1 : 0
        ], [
            'id' => $poll->id
        ]) > 0;
    }

    /**
     * @param $poll_id
     * @throws InvalidArgumentException
     * @return bool
     */
    public function deleteById($poll_id)
    {
        return $this->connect->delete(Utils::table('poll'), ['id' => $poll_id]) > 0;
    }

    /**
     * Find old polls. Limit: 20.
     *
     * @throws DBALException
     * @return array Array of old polls
     */
    public function findOldPolls()
    {
        $prepared = $this->connect->executeQuery('SELECT * FROM ' . Utils::table('poll') . ' WHERE DATE_ADD(end_date, INTERVAL ? DAY) < NOW() AND end_date != 0 LIMIT 20', [PURGE_DELAY]);

        return $prepared->fetchAll();
    }

    /**
     * Search polls in database.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
     * @param int $start The number of first entry to select
     * @param int $limit The number of entries to find
     * @throws DBALException
     * @return array The found polls
     */
    public function findAll($search, $start, $limit) {
        // Polls

        $request  = "";
        $request .= "SELECT p.*,";
        $request .= "    (SELECT count(1) FROM " . Utils::table('vote') . " v WHERE p.id=v.poll_id) votes";
        $request .= " FROM " . Utils::table('poll') . " p";
        $request .= " WHERE 1";

        $values = [];

        if (!empty($search["poll"])) {
            $request .= " AND p.id LIKE :poll";
            $values["poll"] = "{$search["poll"]}%";
        }

        $fields = [
            // key of $search => column name
            "title" => "title",
            "name" => "admin_name",
            "mail" => "admin_mail",
        ];

        foreach ($fields as $searchKey => $columnName) {
            if (empty($search[$searchKey])) {
                continue;
            }

            $request .= " AND p.$columnName LIKE :$searchKey";
            $values[$searchKey] = "%{$search[$searchKey]}%";
        }

        $request .= "  ORDER BY p.title ASC";
        $request .= "  LIMIT :start, :limit";

        $prepared = $this->prepare($request);

        foreach ($values as $searchKey => $value) {
            $prepared->bindParam(":$searchKey", $value, PDO::PARAM_STR);
        }

        $prepared->bindParam(':start', $start, PDO::PARAM_INT);
        $prepared->bindParam(':limit', $limit, PDO::PARAM_INT);

        $prepared->execute();

        return $prepared->fetchAll();
    }

    /**
     * Find all polls that are created with the given admin mail.
     *
     * @param string $mail Email address of the poll admin
     * @throws DBALException
     * @return array The list of matching polls
     */
    public function findAllByAdminMail($mail) {
        $prepared = $this->prepare('SELECT * FROM ' . Utils::table('poll') . ' WHERE admin_mail = :admin_mail');
        $prepared->execute(['admin_mail' => $mail]);

        return $prepared->fetchAll();
    }

    /**
     * Get the total number of polls in database.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @throws DBALException
     * @return int The number of polls
     */
    public function count($search = null) {
        // Total count
        $prepared = $this->prepare('
SELECT count(1) nb
  FROM ' . Utils::table('poll') . ' p
 WHERE (:id = "" OR p.id LIKE :id)
   AND (:title = "" OR p.title LIKE :title)
   AND (:name = "" OR p.admin_name LIKE :name)
 ORDER BY p.title ASC');

        $poll = $search === null ? '' : $search['poll'] . '%';
        $title = $search === null ? '' : '%' . $search['title'] . '%';
        $name = $search === null ? '' : '%' . $search['name'] . '%';
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
