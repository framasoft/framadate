<?php

namespace Framadate\Entity;

class Moment
{
    /**
     * @var string
     */
    private $title;

    /**
     * Moment constructor.
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param $title
     * @return Moment
     */
    public function setTitle($title): Moment
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
