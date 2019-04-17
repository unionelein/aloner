<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

interface PusherDataInterface
{
    public function getType(): int;

    public function getTopicId(): string;
}
