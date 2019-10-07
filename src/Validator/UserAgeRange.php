<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserAgeRange extends Constraint
{
    public $tooYoung = 'Сайт доступен только с {{ minAge }} лет.';

    public $tooOld = 'Сайт доступен до {{ maxAge }} лет.';
}
