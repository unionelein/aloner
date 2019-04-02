<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;

class CityFixture extends BaseFixture
{
    public const CITIES = [
        'Гомель',
        'Минск',
        'Витебск',
        'Гродно',
        'Могилев',
    ];

    protected function loadData()
    {
        $this->createMany('city', \count(self::CITIES), function (int $index) {
            $name = self::CITIES[$index - 1];

            return new City($name);
        });
    }

}