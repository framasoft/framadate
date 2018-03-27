<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha�l DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est r�gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Repository;

use Framadate\Entity\Choice;
use Framadate\Entity\DateChoice;
use Framadate\Entity\Moment;
use Framadate\Utils;

class ChoiceRepository extends AbstractRepository
{

    /**
     * Insert a bulk of choices.
     *
     * @param string $poll_id
     * @param Choice[] $choices
     * @throws \Doctrine\DBAL\DBALException
     */
    public function insertChoices(string $poll_id, array $choices)
    {
        $prepared = $this->prepare('INSERT INTO `' . Utils::table('slot') . '` (poll_id, title, moments) VALUES (?, ?, ?)');

        foreach ($choices as $choice) {
            if ($choice instanceof DateChoice) {
                /** @var DateChoice $choice */
                // We prepared the choices (joined by comas)
                $prepared->execute([$poll_id, $choice->getDate()->getTimestamp(), $this->joinMoments($choice->getMoments())]);
            } else {
                $prepared->execute([$poll_id, $choice->getName(), null]);
            }
        }
    }

    /**
     * @param array $moments
     * @return string
     */
    private function joinMoments(array $moments): string
    {
        $joinedMoments =  '';
        $first = true;
        foreach ($moments as $moment) {
            if ($first) {
                $joinedMoments = $moment;
                $first = false;
            } else {
                $joinedMoments .= ',' . $moment;
            }
        }
        return $joinedMoments;
    }

    /**
     * @param string $poll_id
     * @param boolean $is_date
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function listByPollId(string $poll_id, bool $is_date = false)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? ORDER BY id');
        $prepared->execute([$poll_id]);

        return array_map(function ($array) use ($is_date) {
            return self::mapDataToChoice($array, $is_date);
        }, $prepared->fetchAll());
    }

    /**
     * Find the choice into poll for a given datetime.
     *
     * @param $poll_id int The ID of the poll
     * @param $datetime int The datetime of the choice
     * @return mixed Object The choice found, or null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findByPollIdAndDatetime($poll_id, $datetime)
    {
        $prepared = $this->prepare('SELECT * FROM `' . Utils::table('slot') . '` WHERE poll_id = ? AND SUBSTRING_INDEX(title, \'@\', 1) = ?');

        $prepared->execute([$poll_id, $datetime]);
        $choice = $prepared->fetch();
        $prepared->closeCursor();

        return $choice;
    }

    /**
     * Insert a new choice into a given poll.
     *
     * @param Choice $choice
     * @return Choice
     */
    public function insertChoice(Choice $choice)
    {
        $this->connect->insert(Utils::table('slot'), [
            'poll_id' => $choice->getPollId(),
            'title' => $choice->getName(),
            'moments' => null,
        ]);
        return $choice->setId($this->connect->lastInsertId());
    }

    /**
     * Insert a new choice into a given poll.
     *
     * @param DateChoice $choice
     * @return Choice
     */
    public function insertDateChoice(DateChoice $choice)
    {
        $this->connect->insert(Utils::table('slot'), [
            'poll_id' => $choice->getPollId(),
            'title' => $choice->getDate()->getTimestamp(),
            'moments' => $this->joinMoments($choice->getMoments()),
        ]);
        return $choice->setId($this->connect->lastInsertId());
    }

    /**
     * Update a choice into a poll.
     *
     * @param string $poll_id The ID of the poll
     * @param \DateTime $datetime The datetime of the choice to update
     * @param string $newMoments The new moments
     * @return int true if action succeeded.
     */
    public function update(string $poll_id, \DateTime $datetime, string $newMoments)
    {
        return $this->connect->update(Utils::table('slot'), [
            'moments' => $newMoments,
        ], [
            'poll_id' => $poll_id,
            'title' => $datetime->getTimestamp(),
        ]) > 0;
    }

    /**
     * Delete a entire choice from a poll.
     *
     * @param string $poll_id The ID of the poll
     * @param \DateTime $datetime The datetime of the choice
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByDateTime(string $poll_id, \DateTime $datetime): bool
    {
        return $this->connect->delete(Utils::table('slot'), [
            'poll_id' => $poll_id,
            'title' => $datetime->getTimestamp(),
        ]) > 0;
    }

    /**
     * @param string $poll_id
     * @param string $title
     * @return bool
     * @throws \Doctrine\DBAL\Exception\InvalidArgumentException
     */
    public function deleteByTitle(string $poll_id, string $title): bool
    {
        return $this->connect->delete(Utils::table('slot'), [
                'poll_id' => $poll_id,
                'title' => $title,
            ]) > 0;
    }

    /**
     * @param string $poll_id
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteByPollId(string $poll_id)
    {
        return $this->connect->delete(Utils::table('slot'), [
            'poll_id' => $poll_id,
        ]) > 0;
    }

    /**
     * @param array $choice_data
     * @param bool $is_date
     * @return DateChoice|Moment
     */
    public static function mapDataToChoice(array $choice_data, bool $is_date)
    {
        if ($is_date) {
            $choice = new DateChoice();
            $choice->setMoments(self::mapDataToMoment($choice_data['moments']))
                ->setDate((new \DateTime())->setTimestamp($choice_data['title']));
        } else {
            $choice = new Choice();
        }
        $choice->setId($choice_data['id'])
            ->setName($choice_data['title'])
            ->setPollId($choice_data['poll_id']);
        return $choice;
    }

    /**
     * @param string $moment_data
     * @return Moment[]
     */
    public static function mapDataToMoment(string $moment_data): array
    {
        $moments = explode(',', $moment_data);
        return array_map(function ($moment) {
            return new Moment($moment);
        }, $moments);
    }
}
