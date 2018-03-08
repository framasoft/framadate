<?php

namespace Framadate\Entity;

use DateTime;

class DateSlot extends Slot
{
    /**
     * @var DateTime
     */
    protected $title;

    /**
     * @var string
     */
    private $moments;

    /**
     * @return DateTime
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param DateTime $title
     * @return Slot
     */
    public function setTitle($title): Slot
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMoments(): ?string
    {
        return $this->moments;
    }

    /**
     * @param string $moments
     * @return Slot
     */
    public function setMoments(?string $moments): Slot
    {
        $this->moments = $moments;
        return $this;
    }
}
