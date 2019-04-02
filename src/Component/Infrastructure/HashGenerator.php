<?php declare(strict_types=1);

namespace App\Component\Infrastructure;

class HashGenerator
{
    public static function createUnique(): string
    {
        return \md5(\uniqid(\rand(), true)) . time();
    }
}