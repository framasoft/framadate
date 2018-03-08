<?php

namespace Framadate;

class Vote
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $poll_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $choices;

    /**
     * @var string
     */
    private $uniqId;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Vote
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPollId()
    {
        return $this->poll_id;
    }

    /**
     * @param string $poll_id
     * @return Vote
     */
    public function setPollId($poll_id)
    {
        $this->poll_id = $poll_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Vote
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param string $choices
     * @return Vote
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
        return $this;
    }

    /**
     * @return string
     */
    public function getUniqId()
    {
        return $this->uniqId;
    }

    /**
     * @param string $uniqId
     * @return Vote
     */
    public function setUniqId($uniqId)
    {
        $this->uniqId = $uniqId;
        return $this;
    }
}
