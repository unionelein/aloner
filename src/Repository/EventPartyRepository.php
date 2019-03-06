<?php

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
        $this->createQueryBuilder('event_party')
            ->innerJoin('event_party.event', 'event')
            ->andWhere('event.city = :userCity')
            ->andWhere('event_party.meetingAt')
            ->setParameter('userCity', $user->getCity())
            ->getQuery()
            ->getResult();
    }
}
