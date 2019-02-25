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

            return $event;
        });
    }
}