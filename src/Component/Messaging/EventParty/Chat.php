<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty;

use App\Component\Messaging\EventParty\Model\Chat\ClientDataCollection;
use App\Component\Messaging\EventParty\Model\Chat\ClientData;
use App\Entity\EventParty;
use App\Entity\EventPartyMessage;
use App\Entity\User;
use App\Repository\EventPartyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    private const TYPE_IDENTIFY = 'identify';

    private const TYPE_MESSAGE = 'message';

    /** @var ClientDataCollection */
    protected $clientCollection;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository  */
    private $userRepo;

    /** @var EventPartyRepository */
    private $epRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->clientCollection  = new ClientDataCollection();

        $this->em       = $em;
        $this->userRepo = $em->getRepository(User::class);
        $this->epRepo   = $em->getRepository(EventParty::class);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $json)
    {
        $data = \json_decode($json, true);

        switch ($data['type'] ?? null) {
            case self::TYPE_IDENTIFY:
                $this->identify($from, $data);
                break;
            case self::TYPE_MESSAGE:
                $this->message($from, $data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clientCollection->removeByConnection($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function identify(ConnectionInterface $from, array $data)
    {
        if (empty($data['userTempHash']) || empty($data['eventPartyId'])) {
            $from->close();
            return;
        }

        $this->checkDBConnection();

        $eventParty = $this->epRepo->find((int) $data['eventPartyId']);
        $user       = $this->userRepo->findByTempHash((string) $data['userTempHash']);

        if (!$user || !$eventParty) {
            $from->close();
            return;
        }

        $this->em->refresh($eventParty);
        $this->em->refresh($user);

        if (!$eventParty->getUsers()->contains($user)) {
            $from->close();
            return;
        }

        $this->clientCollection->add(new ClientData($from, $user, $eventParty));

        echo "Identified user: '{$user->getName()}' ({$from->resourceId})\n";
    }

    private function checkDBConnection(): void
    {
        if ($this->em->getConnection()->ping() === false) {
            $this->em->getConnection()->close();
            $this->em->getConnection()->connect();
        }
    }

    private function message(ConnectionInterface $from, array $data)
    {
        $clientData = $this->clientCollection->findByConnection($from);

        if (!$clientData || empty($data['message']) || \strlen($data['message']) > EventPartyMessage::MAX_MESSAGE_LENGTH) {
            return;
        }

        $msg = [
            'username' => $clientData->getUsername(),
            'message'  => \htmlspecialchars($data['message']),
        ];

        $connections = $this->clientCollection->getConnectionsForEventPartyId($clientData->getEventPartyId());

        foreach ($connections as $connection) {
            $connection->send(\json_encode($msg));
        }

        $this->storeMessage($clientData, $data['message']);

        echo "New message from {$msg['username']}: {$data['message']}\n";
    }

    private function storeMessage(ClientData $clientData, string $message)
    {
        $this->checkDBConnection();

        $this->em->getConnection()->executeQuery('
            INSERT INTO event_party_message SET 
            user_id        = :userId,
            event_party_id = :eventPartyId,
            message        = :message,
            created_at     = NOW()
        ', [
            'userId'       => $clientData->getUserId(),
            'eventPartyId' => $clientData->getEventPartyId(),
            'message'      => $message,
        ]);
    }
}
