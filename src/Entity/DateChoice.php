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
namespace Framadate\Entity;

class DateChoice extends Choice
{

    /**
     * All available moments for this Choice.
     * @var Moment[]
     */
    private $moments;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * Choice constructor.
     */
    public function __construct()
    {
        $this->date = null;
        $this->moments = [];
        parent::__construct();
    }


    /**
     * @return \DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        if ($this->getDate()) {
            return $this->getDate()->format('Y-m-d H:i:s');
        }
        return null;
    }

    /**
     * @param string|\DateTime $name
     * @return Choice
     */
    public function setName($name): Choice
    {
        if ($name instanceof \DateTime) {
            $this->date = $name;
            return $this;
        }
        $this->date = (new \DateTime())->setTimestamp(intval($name));
        return $this;
    }

    /**
     * @param \DateTime $date
     * @return DateChoice
     */
    public function setDate(?\DateTime $date): DateChoice
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @param Moment $moment
     * @return Choice
     */
    public function addMoment(Moment $moment): Choice
    {
        $this->moments[] = $moment;
        return $this;
    }

    /**
     * @return array
     */
    public function getMoments(): array
    {
        $this->sortMoments();
        return $this->moments;
    }

    /**
     * @param Moment[] $moments
     * @return DateChoice
     */
    public function setMoments(array $moments): DateChoice
    {
        $this->moments = $moments;
        return $this;
    }

    /**
     * @param DateChoice $a
     * @param DateChoice $b
     * @return bool
     */
    public static function compareDate(DateChoice $a, DateChoice $b)
    {
        return $a->getDate() > $b->getDate();
    }

    /**
     * Clear empty moments submitted by forms
     */
    public function clearEmptyMoments()
    {
        foreach ($this->moments as $index => $moment) {
            /** @var $moment Moment */
            if ($moment == null || empty($moment->getTitle())) {
                unset($this->moments[$index]);
            }
        }
    }

    /**
     * Sort Moments correctly
     */
    public function sortMoments()
    {
        uasort($this->moments, function (Moment $a, Moment $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });
    }
}
