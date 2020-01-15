<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Component\EventParty\Helper\PeopleCompositionFiller;
use App\Entity\Event;
use App\Entity\EventParty;

class EventPartyFixture extends BaseFixture
{
    public const EP_COUNT = 5;

    public function loadData(): void
    {
        $this->createMany('event_party', self::EP_COUNT, function (int $index) {
            /** @var Event $event */
            $event = $this->getReference('event_' . $this->faker->numberBetween(1, EventFixture::EVENTS_COUNT));

            $composition = PeopleCompositionFiller::fillFromRange($event->getPeopleRange());

            return new EventParty($event, $composition);
        });
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            EventFixture::class,
        ];
    }
}
