<?php

namespace App\Repository;

use App\Entity\EventPartyHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventPartyHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventPartyHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventPartyHistory[]    findAll()
 * @method EventPartyHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPartyHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventPartyHistory::class);
    }

    // /**
    //  * @return EventPartyHistory[] Returns an array of EventPartyHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventPartyHistory
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
