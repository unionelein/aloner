<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty;

use Ratchet\ConnectionInterface;
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

    private $eventPartyTopics = [];

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        echo 'new subscribe :' . $topic->getId();
        $this->eventPartyTopics[$topic->getId()] = $topic;
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

        echo $topicKey .' given as topic';

        if (!$topicKey || !$type || !$pusherData) {
            return;
        }

        if (!\in_array($type, self::TYPES, true)) {
            return;
        }

        if (!\array_key_exists($topicKey, $this->eventPartyTopics)) {
            return;
        }

        $topic = $this->eventPartyTopics[$topicKey];

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
}
