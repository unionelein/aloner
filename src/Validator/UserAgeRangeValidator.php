<?php

namespace App\Validator;

use App\Component\EventParty\AgeChecker;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserAgeRangeValidator extends ConstraintValidator
{
    /**
     * @param \DateTime $birthday
     * @param  UserAgeRange $constraint
     */
    public function validate($birthday, Constraint $constraint)
    {
        $age = (int) $birthday->diff(new \DateTime())->format('%y');

        [$minAge, $maxAge] = AgeChecker::getFullRange();

        if ($age < $minAge) {
            $this->context->buildViolation($constraint->tooYoung)
                ->setParameter('{{ minAge }}', $minAge)
                ->addViolation();
        }

        if ($age > $maxAge) {
            $this->context->buildViolation($constraint->tooOld)
                ->setParameter('{{ maxAge }}', $maxAge)
                ->addViolation();
        }
    }
}
