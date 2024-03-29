<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventParty;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EventParty|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventParty|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventParty[]    findAll()
 * @method EventParty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPartyRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventParty::class);
    }

    /**
     * @param User $user
     *
     * @return EventParty[]
     */
    public function findEPsForUser(User $user): array
    {
        return $this->createQueryBuilder('ep')
            ->select('ep, event')
            ->innerJoin('ep.event', 'event')
            ->andWhere('ep.status = :statusPending')
            ->andWhere('event.city = :userCity')
            ->setParameter('statusPending', EventParty::STATUS_PENDING)
            ->setParameter('userCity', $user->getCity())
            ->getQuery()
            ->getResult();
    }
}
