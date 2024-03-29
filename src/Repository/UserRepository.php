<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $tempHash
     * @return User|null
     */
    public function findByTempHash(string $tempHash): ?User
    {
        return $this->createQueryBuilder('user')
            ->andWhere('user.tempHash = :tempHash')
            ->setParameter('tempHash', $tempHash)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return User
     */
    public function getWebUser(): User
    {
        $user = $this->find(User::WEB_ID);
        Assert::notNull($user, 'Web user does not exists');

        return $user;
    }
}
