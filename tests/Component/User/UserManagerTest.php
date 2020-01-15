<?php declare(strict_types=1);

namespace App\Tests\Component\User;

use App\Component\User\UserManager;
use App\Entity\City;
use App\Entity\EventParty;
use App\Entity\VO\PeopleComposition;
use App\Repository\UserRepository;
use App\Tests\MotherObject\EventMother;
use App\Tests\MotherObject\UserMother;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @group integration
 */
class UserManagerTest extends KernelTestCase
{
    /** @var EntityManager */
    private $em;

    /** @var UserRepository|\PHPUnit\Framework\MockObject\MockObject  */
    private $userRepoMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject|EventDispatcherInterface */
    private $dispatcherMock;

    /** @var UserManager */
    private $userManager;

    public function setUp(): void
    {
        self::bootKernel();

        $this->em             = self::$container->get('doctrine')->getManager();
        $this->userRepoMock   = $this->createMock(UserRepository::class);
        $this->dispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $this->userManager = new UserManager($this->userRepoMock, $this->dispatcherMock, $this->em);
    }

    public function testThatUserAddsToEP(): void
    {
        $city = new City('city');
        $this->em->persist($city);

        $event = EventMother::create($city);
        $this->em->persist($event);

        $user = UserMother::withTodayCriteria($city);
        $this->em->persist($user);

        $composition = new PeopleComposition(3, 3);
        $eventParty  = new EventParty($event, $composition);
        $this->em->persist($eventParty);

        $this->userManager->join($user, $eventParty);

        $this->assertTrue($eventParty->getUsers()->contains($user));
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}