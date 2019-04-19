<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;

abstract class AbstractPusherData implements PusherDataInterface, \JsonSerializable
{
    protected $type;

    public function __construct(string $type)
    {
        if (!\in_array($type, Pusher::TYPES, true)) {
            throw new \InvalidArgumentException('Invalid type given');
        }

        $this->type = $type;
    }
    
    abstract public function getTopicId(): string;

    abstract public function toArray(): array;
    
    public function getType(): string
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
