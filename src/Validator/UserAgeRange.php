<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserAgeRange extends Constraint
{
    public $tooYoung = 'Сайт доступен только с {{ minAge }} лет.';

    public $tooOld = 'Сайт доступен только до {{ maxAge }} лет.';
}
