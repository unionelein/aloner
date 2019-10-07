<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

class UserAgeRangeValidator extends ConstraintValidator
{
    private const MIN_AGE = 15;

    private const MAX_AGE = 55;

    /**
     * @param \DateTime $birthday
     * @param  UserAgeRange $constraint
     */
    public function validate($birthday, Constraint $constraint): void
    {
        Assert::isInstanceOf($constraint, UserAgeRange::class);
        Assert::isInstanceOf($birthday, \DateTime::class);

        $age = (int) $birthday->diff(new \DateTime())->format('%y');

        if ($age < self::MIN_AGE) {
            $this->context->buildViolation($constraint->tooYoung)
                ->setParameter('{{ minAge }}', self::MIN_AGE)
                ->addViolation();
        }

        if ($age > self::MAX_AGE) {
            $this->context->buildViolation($constraint->tooOld)
                ->setParameter('{{ maxAge }}', self::MAX_AGE)
                ->addViolation();
        }
    }
}
