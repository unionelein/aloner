<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventPartyMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EventPartyMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventPartyMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventPartyMessage[]    findAll()
 * @method EventPartyMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventPartyMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EventPartyMessage::class);
    }

    // /**
    //  * @return Chat[] Returns an array of Chat objects
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
    public function findOneBySomeField($value): ?Chat
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
