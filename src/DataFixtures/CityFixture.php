<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;

class CityFixture extends BaseFixture
{
    /**
     * @return int
     */
    public static function citiesCount(): int
    {
        return \count(self::CITIES);
    }

    private const CITIES = [
        'Гомель',
        'Минск',
        'Витебск',
        'Гродно',
        'Могилев',
    ];

    protected function loadData(): void
    {
        $this->createMany('city', self::citiesCount(), function (int $index) {
            $name = self::CITIES[$index - 1] ?? self::CITIES[0];

            return new City($name);
        });
    }
}
