<?php declare(strict_types=1);

namespace App\Component\EventParty;

class AgeChecker
{
    private const AGE_INTERVALS = [
        // allowed years different, ages
        1 => [0, 15],
        2 => [16, 20],
        3 => [21, 30],
        4 => [31, 50],
        5 => [51, 100],
    ];

    public static function check(array $usersAge, int $age): bool
    {
        if (\count($usersAge) === 0) {
            return true;
        }

        $avgAge = (int) (\array_sum($usersAge) / \count($usersAge));
        $range  = self::getAgeRange($avgAge);

        if ($age >= $range[0] && $age <= $range[1]) {
            return true;
        }

        return false;
    }

    public static function getAgeRange(int $age): array
    {
        foreach (self::AGE_INTERVALS as $interval => $ageRange) {
            if ($age >= $ageRange[0] && $age <= $ageRange[1]) {
                return [$age - $interval, $age + $interval];
            }
        }

        return [0, 0];
    }
}