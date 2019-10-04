<?php declare(strict_types=1);

namespace App\Component\Infrastructure;

class HashGenerator
{
    /**
     * @return string
     */
    public static function createUnique(): string
    {
        return \md5(\uniqid((string) \mt_rand(), true)) . time();
    }
}
