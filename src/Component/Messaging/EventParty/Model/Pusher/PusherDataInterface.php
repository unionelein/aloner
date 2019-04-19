<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

interface PusherDataInterface
{
    public function getType(): string;

    public function getTopicId(): string;
}
