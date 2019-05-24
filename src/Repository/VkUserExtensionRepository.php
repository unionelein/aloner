<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\VkUserExtension;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method VkUserExtension|null find($id, $lockMode = null, $lockVersion = null)
 * @method VkUserExtension|null findOneBy(array $criteria, array $orderBy = null)
 * @method VkUserExtension[]    findAll()
 * @method VkUserExtension[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VkUserExtensionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, VkUserExtension::class);
    }

    // /**
    //  * @return VkUserToken[] Returns an array of VkUserToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?VkUserToken
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
