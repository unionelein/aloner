<?php

namespace App\DataFixtures;


use App\Entity\Event;

class EventFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany('event', 10, function (int $index) {
            $event = new Event();
            $event->setTitle($this->faker->company);
            $event->setDescription($this->faker->text);
            $event->setCity($this->getReference('city_' . \random_int(1, 2)));

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