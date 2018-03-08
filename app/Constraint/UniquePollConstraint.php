<?php

namespace Framadate\Constraint;

use Symfony\Component\Validator\Constraint;

class UniquePollConstraint extends Constraint
{
    public $message = 'The poll {{ poll }} already exists';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return UniquePollConstraintValidator::class;
    }
}
