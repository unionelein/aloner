<?php

namespace App\Component\Messaging;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    private const TYPE_IDENTIFY = 'identify';

    private const TYPE_MESSAGE = 'message';

    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

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
                $this->message($data);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    private function identify(ConnectionInterface $from, array $data)
    {
        $data['userTempHash'];
    }

    private function message(array $data)
    {
        foreach ($this->clients as $client) {
            $client->send($data);
        }
    }
}