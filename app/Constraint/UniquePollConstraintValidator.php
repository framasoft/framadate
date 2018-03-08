<?php

namespace Framadate\Constraint;

use Framadate\Services\PollService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniquePollConstraintValidator extends ConstraintValidator
{
    /**
     * @var PollService
     */
    private $poll_service;

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        // TODO : Check why Poll Service can't be injected
        if ($this->poll_service && $this->poll_service->existsById($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ poll }}', $value)
                ->addViolation();
        }
        // TODO : Translate message
    }

    public function setPollService($poll_service)
    {
        $this->poll_service = $poll_service;
    }
}
