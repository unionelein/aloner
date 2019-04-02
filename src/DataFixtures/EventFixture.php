<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Event;

class EventFixture extends BaseFixture
{
    public const EVENT_TITLES = [
        'Батутная арена Neo Land',
        'Боулинг на спартаке',
        'Прыжки с парашютом',
        'Клуб Мята',
        'Квест Тайная комната',
        'Квест Побег',
        'Квест Паранойа',
        'Бильярд',
        'Ночная тусовка на батутах',
        'Музей картин 1950-ых',
        'Зоопарк',
        'Парк аттракционов',
        'Прогулка по парку румянцевых-паскевичей',
        'Картинг',
        'Казино "Твоя удача"',
    ];

    public function loadData()
    {
        $this->createMany('event', 20, function (int $index) {
            /** @var City $city */
            $city = $this->getReference('city_' . \rand(1, \count(CityFixture::CITIES)));

            $event = new Event();
            $event->setTitle($this->faker->randomElement(self::EVENT_TITLES));
            $event->setDescription($this->faker->text);
            $event->setCity($city);

            return $event;
        });
    }

    public function getDependencies()
    {
        return [
            CityFixture::class,
        ];
    }
}