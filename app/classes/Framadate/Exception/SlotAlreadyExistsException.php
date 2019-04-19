<?php
namespace Framadate\Exception;

class SlotAlreadyExistsException extends \Exception {
    public $slot;

    public function __construct($slot, $message = '')
    {
        parent::__construct($message);
        $this->slot = $slot;
    }

    /**
     * @return mixed
     */
    public function getSlot()
    {
        return $this->slot;
    }
}
