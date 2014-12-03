<?php
namespace Framadate;

class Choice
{
    /**
     * Name of the Choice
     */
    private $name;
    
    /**
     * All availables slots for this Choice.
     */
    private $slots;
    
    public function __construct($name)
    {
        $this->name = $name;
        $this->slots = array();
    }
    
    public function addSlot($slot)
    {
        $this->slots[] = $slot;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSlots()
    {
        return $this->slots;
    }
    
    static function compare(Choice $a, Choice $b)
    {
        return strcmp($a->name, $b->name);
    }
    
}
