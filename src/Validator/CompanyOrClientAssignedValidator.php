<?php

namespace App\Validator;

use App\Entity\Client;
use App\Entity\Company;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CompanyOrClientAssignedValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof CompanyOrClientAssigned) {
            throw new UnexpectedTypeException($constraint, CompanyOrClientAssigned::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        // in our case however we are checking exactly for null and empty values
//        if (null === $value || '' === $value) {
//            return;
//        }

        if (
            (empty($object->getCompany()) and empty($object->getClient())) or
            (!$object->getCompany() instanceof Company and !$object->getClient() instanceof Client)
        ) {
            // the argument must be a string or an object implementing __toString()
            $this->context->buildViolation($constraint->message)
                ->atPath('company')
                ->addViolation();
        }
    }
}