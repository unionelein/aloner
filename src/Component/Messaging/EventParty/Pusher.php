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
    public const TYPE_JOIN = 1;

    public const TYPE_SKIP = 2;


    public const TYPE_TIME_OFFER = 3;

    public const TYPE_PLACE_OFFER = 4;

    public const TYPE_CAFE_OFFER = 5;


    public const TYPE_TIME_OFFER_ANSWER = 6;

    public const TYPE_PLACE_OFFER_ANSWER = 7;

    public const TYPE_CAFE_OFFER_ANSWER = 8;

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
    private $eventPartyTopics = [];

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

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        echo 'new subscribe: ' . $topic->getId() . "\n";

        $topic->remove($conn);

        $data = \explode('|', $topic->getId());

        if (!isset($data[0], $data[1])) {
            return;
        }

        [$epId, $userHash] = $data;

        $this->checkDBConnection();

        $eventParty = $this->epRepo->find((int) $epId);
        $user       = $this->userRepo->findByTempHash($userHash);

        if (!$user || !$eventParty || !$eventParty->getUsers()->contains($user)) {
            return;
        }

        $this->eventPartyTopics[$epId] = $this->eventPartyTopics[$epId] ?? new Topic((string) $epId);
        $this->eventPartyTopics[$epId]->add($conn);

        echo 'now subs: ' . $this->eventPartyTopics[$epId]->count() . "\n";
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

        echo $topicKey ." given as topic\n";

        if (!$topicKey || !$type || !$pusherData) {
            return;
        }

        if (!\in_array($type, self::TYPES, true)) {
            return;
        }

        if (!\array_key_exists($topicKey, $this->eventPartyTopics)) {
            return;
        }

        $topic = $this->eventPartyTopics[$topicKey] ?? null;

        if (!$topic || $topic->count() === 0) {
            return;
        }

        $topic->broadcast(\json_encode([
            'type' => $type,
            'data' => $pusherData,
        ]));
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
        echo 'unsubscribe: ' . $topic->getId() ."\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
    }

    public function onClose(ConnectionInterface $conn)
    {
        foreach ($this->eventPartyTopics as $epId => $topic) {
            if (!$topic->has($conn)) {
                continue;
            }

            $topic->remove($conn);

            if ($topic->count() === 0) {
                unset($this->eventPartyTopics[$epId]);
            }
        }
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
        // In this application if clients send data it's because the user hacked around in console
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
