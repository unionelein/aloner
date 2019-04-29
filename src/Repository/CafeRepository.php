<?php

namespace App\Repository;

use App\Entity\Cafe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cafe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cafe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cafe[]    findAll()
 * @method Cafe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CafeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cafe::class);
    }

    // /**
    //  * @return Cafe[] Returns an array of Cafe objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cafe
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
