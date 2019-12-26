<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param User $user
     * @return Event[]
     */
    public function findAppropriateEventsForUser(User $user): array
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.city = :userCity')
            ->setParameter('userCity', $user->getCity())
            ->getQuery()
            ->getResult();
    }
}
