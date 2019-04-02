<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\EventParty;

class EventPartyFixture extends BaseFixture
{
    public function loadData()
    {
        $this->createMany('event_party', 10, function (int $index) {
            /** @var Event $event */
            $event = $this->getReference('event_' . \rand(1, \count(EventFixture::EVENT_TITLES)));

            return new EventParty($event, \rand(1, 3), \rand(1, 3));
        });
    }

    public function getDependencies()
    {
        return [
            EventFixture::class,
        ];
    }
}