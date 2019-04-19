<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty;

use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventPartyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;

class Pusher implements WampServerInterface
{
    public const TYPE_JOIN = 'join';

    public const TYPE_SKIP = 'skip';

    public const TYPE_FILLED = 'filled';

    public const TYPE_TIME_OFFER = 'time_offer';

    public const TYPE_PLACE_OFFER = 'place_offer';

    public const TYPE_CAFE_OFFER = 'cafe_offer';


    public const TYPE_TIME_OFFER_ANSWER = 'time_offer_answer';

    public const TYPE_PLACE_OFFER_ANSWER = 'place_offer_answer';

    public const TYPE_CAFE_OFFER_ANSWER = 'cafe_offer_answer';

    public const TYPES = [
        self::TYPE_JOIN,
        self::TYPE_SKIP,
        self::TYPE_TIME_OFFER,
        self::TYPE_PLACE_OFFER,
        self::TYPE_CAFE_OFFER,
        self::TYPE_TIME_OFFER_ANSWER,
        self::TYPE_PLACE_OFFER_ANSWER,
        self::TYPE_CAFE_OFFER_ANSWER,
    ];

    /** @var Topic[] */
    private $topics = [];

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository  */
    private $userRepo;

    /** @var EventPartyRepository */
    private $epRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em       = $em;
        $this->userRepo = $em->getRepository(User::class);
        $this->epRepo   = $em->getRepository(EventParty::class);
    }

    /**
     * @param array $event
     */
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        echo "new subscribe: {$topic->getId()}\n";

        if (empty($event['eventPartyId']) || empty($event['userTempHash'])) {
            $conn->close();
            return;
        }

        $this->checkDBConnection();

        $eventParty = $this->epRepo->find((int) $event['eventPartyId']);
        $user       = $this->userRepo->findByTempHash((string) $event['userTempHash']);

        if (!$user || !$eventParty) {
            $conn->close();
            return;
        }

        $this->em->refresh($eventParty);
        $this->em->refresh($user);

        if (!$eventParty->getUsers()->contains($user)) {
            $conn->close();
            return;
        }

        $this->topics[$topic->getId()] = $this->topics[$topic->getId()] ?? $topic;
        $this->topics[$topic->getId()]->add($conn);

        echo "current count for topic {$topic->getId()}: {$this->topics[$topic->getId()]->count()}\n";
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onMessage(string $json): void
    {
        $data = \json_decode($json, true);

        $topicKey   = $data['topic'] ?? null;
        $type       = $data['type'] ?? null;
        $pusherData = $data['data'] ?? null;

        if (!$topicKey || !$type || !$pusherData) {
            return;
        }

        if (!\in_array($type, self::TYPES, true)) {
            return;
        }

        if (!\array_key_exists($topicKey, $this->topics)) {
            return;
        }

        $topic = $this->topics[$topicKey] ?? null;

        if (!$topic || $topic->count() === 0) {
            return;
        }

        echo "Send data {$json}\n";

        $topic->broadcast(\json_encode([
            'type' => $type,
            'data' => $pusherData,
        ]));
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->topics as $topicId => $topic) {
            if (!$topic->has($conn)) {
                continue;
            }

            $topic->remove($conn);

            if ($topic->count() === 0) {
                unset($this->topics[$topicId]);
            }

            echo "close conn, count in topic {$topic->getId()}: {$topic->count()}, topics count: " . \count($this->topics) . "\n";
        }
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $conn->close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    private function checkDBConnection(): void
    {
        if ($this->em->getConnection()->ping() === false) {
            $this->em->getConnection()->close();
            $this->em->getConnection()->connect();
        }
    }
}
