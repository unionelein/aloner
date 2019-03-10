<?php

namespace App\DataFixtures;

use App\Entity\EventParty;

class EventPartyFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany('event_party', 10, function (int $index) {
            $eventParty = new EventParty();
            $eventParty->setEvent($this->getReference('event_' . \random_int(1, 10)));
            $eventParty->setNumberOfGirls(2);
            $eventParty->setNumberOfGuys(2);

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