<?php

namespace Framadate\Entity;

class Slot
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    protected $poll_id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Slot
     */
    public function setId(string $id): Slot
    {
        $this->id = $id;
        return $this;
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
    public function getPollId(): string
    {
        return $this->poll_id;
    }

    /**
     * @param string $poll_id
     * @return Slot
     */
    public function setPollId(string $poll_id): Slot
    {
        $this->poll_id = $poll_id;
        return $this;
    }
}
