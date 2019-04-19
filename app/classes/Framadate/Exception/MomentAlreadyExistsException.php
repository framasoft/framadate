<?php
namespace Framadate\Exception;

class MomentAlreadyExistsException extends SlotAlreadyExistsException {
    public $moment;

    public function __construct($slot, $moment, $message = '')
    {
        parent::__construct($slot, $message);
        $this->moment = $moment;
    }

    /**
     * @return mixed
     */
    public function getMoment()
    {
        return $this->moment;
    }
}
