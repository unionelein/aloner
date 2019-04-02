<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use App\Entity\EventPartyMessage;
use App\Entity\User;
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

    /**
     * @param User $user
     * @param EventParty $eventParty
     *
     * @return EventPartyMessage[]
     */
    public function getMessageHistoryFor(EventParty $eventParty, User $user = null): array
    {
        $qb = $this->createQueryBuilder('chat_msg');

        if ($user) {
            $historyJoin = $user->getLastEPHistoryFor($eventParty, EventPartyHistory::ACTION_JOIN);

            if (!$historyJoin) {
                return [];
            }

            $qb->andWhere('chat_msg.createdAt > :joinedAt')
                ->setParameter('joinedAt', $historyJoin->getCreatedAt());
        }

        return $qb->getQuery()
            ->getResult();
    }
}
