<?php

namespace App\Repository;

use App\Entity\SearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SearchCriteria|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchCriteria|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchCriteria[]    findAll()
 * @method SearchCriteria[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchCriteriaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SearchCriteria::class);
    }

    // /**
    //  * @return SearchCriteria[] Returns an array of SearchCriteria objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchCriteria
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
