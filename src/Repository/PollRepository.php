<?php
namespace Framadate\Repository;

use Framadate\Entity\Poll;
use Framadate\Utils;
use PDO;

class PollRepository extends AbstractRepository
{

    /**
     * @param array $poll_data
     * @return Poll
     */
    public static function mapDataToPoll(array $poll_data)
    {
        $poll = new Poll();
        $poll->setId($poll_data['id'])
            ->setAdminId($poll_data['admin_id'])
            ->setTitle($poll_data['title'])
            ->setFormat($poll_data['format'])
            ->setDescription($poll_data['description'])
            ->setAdminName($poll_data['admin_name'])
            ->setAdminMail($poll_data['admin_mail'])
            ->setEndDate(\DateTime::createFromFormat('Y-m-d H:i:s', $poll_data['end_date']))
            ->setEditable($poll_data['editable'])
            ->setReceiveNewVotes($poll_data['receiveNewVotes'])
            ->setReceiveNewComments($poll_data['receiveNewComments'])
            ->setHidden($poll_data['hidden'])
            ->setPasswordHash($poll_data['password_hash'])
            ->setResultsPubliclyVisible($poll_data['results_publicly_visible'])
            ->setValueMax($poll_data['ValueMax'])
            ->setActive($poll_data['active']);

        return $poll;
    }

    /**
     * @param Poll $poll
     */
    public function insertPoll(Poll $poll)
    {
        $this->connect->insert(
            Utils::table('poll'),
            [
                'id' => $poll->getId(),
                'admin_id' => $poll->getAdminId(),
                'title' => $poll->getTitle(),
                'description' => $poll->getDescription(),
                'admin_name' => $poll->getAdminName(),
                'admin_mail' => $poll->getAdminMail(),
                'end_date' => $poll->getEndDate()->format('Y-m-d H:i:s'),
                'format' => $poll->getFormat(),
                'editable' => ($poll->getEditable() >= 0 && $poll->getEditable() <= 2) ? $poll->getEditable() : 0,
                'receiveNewVotes' => $poll->getReceiveNewVotes() ? 1 : 0,
                'receiveNewComments' => $poll->getReceiveNewComments() ? 1 : 0,
                'hidden' => $poll->isHidden() ? 1 : 0,
                'password_hash' => $poll->getPasswordHash(),
                'results_publicly_visible' => $poll->isResultsPubliclyVisible() ? 1 : 0,
                'ValueMax' => $poll->getValueMax(),
            ]
        );
    }

    /**
     * @param $poll_id
     * @return Poll
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findById($poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute([$poll_id]);
        $poll_data = $prepared->fetch(PDO::FETCH_ASSOC);
        $prepared->closeCursor();

        if ($poll_data) {
            return $this->mapDataToPoll($poll_data);
        }
        return null;
    }

    /**
     * @param $admin_poll_id
     * @return Poll
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByAdminId($admin_poll_id)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE admin_id = ?');

        $prepared->execute([$admin_poll_id]);
        $poll_data = $prepared->fetch(PDO::FETCH_ASSOC);
        $prepared->closeCursor();

        if ($poll_data) {
            $poll = new Poll();
            $poll->setId($poll_data['id'])
                ->setAdminId($poll_data['admin_id'])
                ->setTitle($poll_data['title'])
                ->setDescription($poll_data['description'])
                ->setFormat($poll_data['format'])
                ->setAdminName($poll_data['admin_name'])
                ->setAdminMail($poll_data['admin_mail'])
                ->setEndDate(\DateTime::createFromFormat('Y-m-d H:i:s', $poll_data['end_date']))
                ->setEditable($poll_data['editable'])
                ->setReceiveNewVotes($poll_data['receiveNewVotes'])
                ->setReceiveNewComments($poll_data['receiveNewComments'])
                ->setHidden($poll_data['hidden'])
                ->setPasswordHash($poll_data['password_hash'])
                ->setResultsPubliclyVisible($poll_data['results_publicly_visible'])
                ->setValueMax($poll_data['ValueMax'])
                ->setActive($poll_data['active']);

            return $poll;
        }
        return null;
    }

    /**
     * @param $poll_id
     * @return bool
     */
    public function existsById($poll_id)
    {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('poll') . '` WHERE id = ?');

        $prepared->execute([$poll_id]);

        return $prepared->rowCount() > 0;
    }

    /**
     * @param $admin_poll_id
     * @return bool
     */
    public function existsByAdminId($admin_poll_id)
    {
        $prepared = $this->prepare('SELECT 1 FROM `' . Utils::table('poll') . '` WHERE admin_id = ?');

        $prepared->execute([$admin_poll_id]);

        return $prepared->rowCount() > 0;
    }

    /**
     * @param Poll $poll
     * @return bool
     */
    public function update(Poll $poll)
    {
        $nb_rows = $this->connect->update(
            Utils::table('poll'),
            [
                'id' => $poll->getId(),
                'admin_id' => $poll->getAdminId(),
                'title' => $poll->getTitle(),
                'description' => $poll->getDescription(),
                'admin_name' => $poll->getAdminName(),
                'admin_mail' => $poll->getAdminMail(),
                'end_date' => $poll->getEndDate()->format('Y-m-d H:i:s'),
                'format' => $poll->getFormat(),
                'editable' => ($poll->getEditable() >= 0 && $poll->getEditable() <= 2) ? $poll->getEditable() : 0,
                'receiveNewVotes' => $poll->getReceiveNewVotes() ? 1 : 0,
                'receiveNewComments' => $poll->getReceiveNewComments() ? 1 : 0,
                'hidden' => $poll->isHidden() ? 1 : 0,
                'password_hash' => $poll->getPasswordHash(),
                'results_publicly_visible' => $poll->isResultsPubliclyVisible() ? 1 : 0,
                'ValueMax' => $poll->getValueMax(),
            ],
            [
                'id' => $poll->getId(),
            ]
        );
        return $nb_rows > 0;
    }

    /**
     * @param $poll_id
     * @return bool
     */
    public function deleteById($poll_id)
    {
        $prepared = $this->prepare('DELETE FROM `' . Utils::table('poll') . '` WHERE id = ?');

        return $prepared->execute([$poll_id]);
    }

    /**
     * Find old polls. Limit: 20.
     *
     * @param $purge_delay
     * @return array Array of old polls
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOldPolls($purge_delay)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE DATE_ADD(`end_date`, INTERVAL ' . $purge_delay . ' DAY) < NOW() AND `end_date` != 0 LIMIT 20');
        $prepared->execute([]);

        return array_map("self::mapDataToPoll", $prepared->fetchAll());
    }

    /**
     * Search polls in databse.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>..., 'mail'=>...]
     * @param int $start The number of first entry to select
     * @param int $limit The number of entries to find
     * @return array The found polls
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAll($search, $start, $limit)
    {
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findAllByAdminMail($mail)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('poll') . '` WHERE admin_mail = :admin_mail');
        $prepared->execute(['admin_mail' => $mail]);

        return $prepared->fetchAll();
    }

    /**
     * Get the total number of polls in databse.
     *
     * @param array $search Array of search : ['id'=>..., 'title'=>..., 'name'=>...]
     * @return int The number of polls
     * @throws \Doctrine\DBAL\DBALException
     */
    public function count($search = null)
    {
        // Total count
        $prepared = $this->prepare('
SELECT count(1) nb
  FROM `' . Utils::table('poll') . '` p
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
