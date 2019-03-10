<?php

namespace App\DataFixtures;


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
        $this->createMany('event', \count(self::EVENT_TITLES), function (int $index) {
            $event = new Event();
            $event->setTitle(self::EVENT_TITLES[$this->faker->numberBetween(0, \count(self::EVENT_TITLES) - 1)]);
            $event->setDescription($this->faker->text);
            $event->setCity($this->getReference('city_' . $this->faker->numberBetween(1, \count(CityFixture::CITIES))));

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