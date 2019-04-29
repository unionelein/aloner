<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Cafe;

class CafeFixture extends BaseFixture
{
    public const CAFES = [
        'Кафе Dinner',
        'Ресторан "Илон Маск одобряет"',
        'Bubbles',
        'John Fedor',
    ];

    protected function loadData()
    {
        $this->createMany('cafe', \count(self::CAFES), function (int $index) {
            $name = self::CAFES[$index - 1];

            return new Cafe($name, 'площадь ленина, 44');
        });
    }
}