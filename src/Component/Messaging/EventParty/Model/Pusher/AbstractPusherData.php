<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;

abstract class AbstractPusherData implements PusherDataInterface, \JsonSerializable
{
    protected $type;

    public function __construct(int $type)
    {
        if (!\in_array($type, Pusher::TYPES, true)) {
            throw new \InvalidArgumentException('Invalid type given');
        }

        $this->type = $type;
    }
    
    abstract public function getTopicId(): string;
    
    public function getType(): int
    {
        return $this->type;
    }

    public function jsonSerialize(): array
    {
        return \get_object_vars($this);
    }
}