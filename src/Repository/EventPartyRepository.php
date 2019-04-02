<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventParty;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventParty|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventParty|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventParty[]    findAll()
 * @method EventParty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPartyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventParty::class);
    }

    /**
     * @param User $user
     *
     * @return EventParty[]
     */
    public function findAvailableEventPartiesForUser(User $user): array
    {
        return $this->createQueryBuilder('event_party')
            ->select('event_party, event')
            ->innerJoin('event_party.event', 'event')
            ->andWhere('event_party.status = :pendingStatus')
            ->andWhere('event.city = :userCity')
            ->setParameter('pendingStatus', EventParty::STATUS_PENDING)
            ->setParameter('userCity', $user->getCity())
            ->getQuery()
            ->getResult();
    }
}
