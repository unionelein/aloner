<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Chat;

use Ratchet\ConnectionInterface;

class ClientDataCollection
{
    /** @var ClientData[] */
    private $collection = [];

    public function add(ClientData $clientData)
    {
        $connId = $clientData->getConnection()->resourceId;

        $this->collection[$connId] = $clientData;
    }

    public function findByConnection(ConnectionInterface $connection): ?ClientData
    {
        return $this->collection[$connection->resourceId] ?? null;
    }

    public function removeByConnection(ConnectionInterface $connection): void
    {
        if (isset($this->collection[$connection->resourceId])) {
            unset($this->collection[$connection->resourceId]);
        }
    }

    /**
     * @param int $eventPartyId
     *
     * @return  ClientData[]
     */
    public function getConnectionsForEventPartyId(int $eventPartyId): array
    {
        $data = [];

        foreach ($this->collection as $clientData) {
            if ($clientData->getEventPartyId() === $eventPartyId) {
                $data[] = $clientData->getConnection();
            }
        }

        return $data;
    }
}