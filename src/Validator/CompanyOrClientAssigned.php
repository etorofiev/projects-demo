<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanyOrClientAssigned extends Constraint
{
    public $message = 'Either the company of the client of this entity must be assigned';

    public function validatedBy()
    {
        return static::class.'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}