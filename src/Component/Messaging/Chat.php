<?php

namespace App\Component\Messaging;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    private const TYPE_IDENTIFY = 'identify';

    private const TYPE_MESSAGE = 'message';

    protected $clients;

    protected $clientsData = [];

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository  */
    private $userRepo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->clients = new \SplObjectStorage;
        $this->em = $em;
        $this->userRepo = $em->getRepository(User::class);
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->clientsData[$conn->resourceId] = [];

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $json)
    {
        $data = \json_decode($json, true);

        switch ($data['type'] ?? 0) {
            case self::TYPE_IDENTIFY:
                $this->identify($from, $data);
                break;
            case self::TYPE_MESSAGE:
                $this->message($from, $data);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        unset($this->clientsData[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function identify(ConnectionInterface $from, array $data)
    {
        $this->checkDBConnection();
        $user = $this->userRepo->findByTempHash($data['userTempHash'] ?? '');

        if (!$user instanceof User) {
            return;
        }

        $this->clientsData[$from->resourceId] = [
            'userId'   => $user->getId(),
            'username' => $user->getName(),
        ];

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
        if (empty($data['message']) || empty($this->clientsData[$from->resourceId]['username'])) {
            return;
        }

        $msg = [
            'username' => $this->clientsData[$from->resourceId]['username'],
            'message'  => $data['message'],
        ];

        foreach ($this->clients as $client) {
            $client->send(\json_encode($msg));
        }

        echo "New message from {$msg['username']}: {$msg['message']}\n";
    }
}