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
    /** @var Topic[] */
    private $topics = [];

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository  */
    private $userRepo;

    /** @var EventPartyRepository */
    private $epRepo;

    /**
     * @param EntityManagerInterface $em
     */
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
        $data     = \json_decode($json, true);
        $topicKey = $data['topic'] ?? null;

        if (!\array_key_exists($topicKey, $this->topics)) {
            return;
        }

        echo "Send data {$json}\n";

        $this->topics[$topicKey]->broadcast($json);
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
