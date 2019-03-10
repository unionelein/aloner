<?php

namespace App\DataFixtures;

use App\Entity\EventParty;

class EventPartyFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany('event_party', 10, function (int $index) {
            $eventParty = new EventParty($this->getReference('event_' . $this->faker->numberBetween(1, \count(EventFixture::EVENT_TITLES))));
            $eventParty->setNumberOfGirls($this->faker->numberBetween(2, 3));
            $eventParty->setNumberOfGuys($this->faker->numberBetween(2, 3));

            return $eventParty;
        });
    }

    public function getDependencies()
    {
        return [
            EventFixture::class,
        ];
    }
}