<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventParty;
use App\Entity\EPHistory;
use App\Entity\EPMessage;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method EPMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method EPMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method EPMessage[]    findAll()
 * @method EPMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EPMessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, EPMessage::class);
    }

    /**
     * @param User $user
     * @param EventParty $eventParty
     *
     * @return EPMessage[]
     */
    public function getMessageHistoryFor(EventParty $eventParty, User $user = null): array
    {
        $historyJoin = $user->getLastEPHistoryFor($eventParty, EPHistory::ACTION_JOIN);

        if (!$historyJoin) {
            return [];
        }

        return $this->createQueryBuilder('chat_msg')
            ->andWhere('chat_msg.createdAt > :joinedAt')
            ->setParameter('joinedAt', $historyJoin->getCreatedAt())
            ->getQuery()
            ->getResult();
    }
}
