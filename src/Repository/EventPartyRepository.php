<?php

namespace App\Repository;

use App\Entity\EventParty;
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

    // /**
    //  * @return EventParty[] Returns an array of EventParty objects
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
    public function findOneBySomeField($value): ?EventParty
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
