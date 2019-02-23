<?php

namespace App\DataFixtures;


use App\Entity\Event;

class EventFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany(Event::class, 10, function (Event $event, $index) {
            $event->setTitle($this->faker->company);
        });
    }
}