<?php declare(strict_types=1);

namespace App\Component\EventParty;

class AgeChecker
{
    private const AGE_INTERVALS = [
        [15, 17],
        [18, 22],
        [23, 28],
        [29, 36],
        [37, 45],
        [46, 55],
    ];

    public static function check(array $usersAge, int $age): bool
    {
        if (\count($usersAge) === 0) {
            return true;
        }

        $avgAge = (int) (\array_sum($usersAge) / \count($usersAge));
        $range  = self::getAgeRange($avgAge);

        if ($range && $age >= $range[0] && $age <= $range[1]) {
            return true;
        }

        return false;
    }

    public static function getAgeRange(int $age): array
    {
        foreach (self::AGE_INTERVALS as $ageRange) {
            if ($age >= $ageRange[0] && $age <= $ageRange[1]) {
                return $ageRange;
            }
        }

        return [];
    }

    public static function getTotalRange(): array
    {
        $fromAges = array_column(self::AGE_INTERVALS, 0);
        $toAges   = array_column(self::AGE_INTERVALS, 1);

        return [\min($fromAges), \max($toAges)];
    }
}
