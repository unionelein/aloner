<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event;
use App\Entity\EventParty;
use App\Entity\VO\PeopleComposition;

class EventPartyFixture extends BaseFixture
{
    public const EP_COUNT = 5;

    public function loadData(): void
    {
        $this->createMany('event_party', 5, function (int $index) {
            /** @var Event $event */
            $event = $this->getReference('event_' . $this->faker->numberBetween(1, EventFixture::EVENTS_COUNT));

            $numberOfEachSex = $event->getPeopleRange()->randomEven() / 2;

            return new EventParty($event, new PeopleComposition($numberOfEachSex, $numberOfEachSex));
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
