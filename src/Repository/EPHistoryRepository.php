<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EPHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EPHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method EPHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method EPHistory[]    findAll()
 * @method EPHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EPHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EPHistory::class);
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
