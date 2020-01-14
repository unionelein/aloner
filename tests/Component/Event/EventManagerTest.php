<?php declare(strict_types=1);

namespace App\Tests\Component\Event;

use App\Component\Event\EventManager;
use App\Entity\City;
use App\Repository\EventRepository;
use App\Tests\MotherObject\EventMother;
use App\Tests\MotherObject\UserMother;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class EventManagerTest extends TestCase
{
    /** @var EventRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $eventRepoMock;

    /** @var EventManager */
    private $eventManager;

    public function setUp(): void
    {
        $this->eventRepoMock = $this->createMock(EventRepository::class);
        $this->eventManager  = new EventManager($this->eventRepoMock);
    }

    public function testThatEventFoundsForUser(): void
    {
        $city = new City('Homel');

        $user  = UserMother::withTodayCriteria($city);
        $event = EventMother::withTodayTimeTable($city);

        $this->eventRepoMock->method('findEventsForUser')->willReturn([$event]);

        $foundEvent = $this->eventManager->findForUser($user);

        $this->assertSame($event, $foundEvent);
    }
}