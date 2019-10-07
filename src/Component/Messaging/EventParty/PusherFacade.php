<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Messaging\EventParty;

use App\Component\Messaging\EventParty\Model\Pusher\Data\PusherData;

class PusherFacade
{
    private const ADDRESS = 'tcp://localhost:5555';

    /** @var \ZMQSocket */
    private $socket;

    public function __construct()
    {
        $context      = new \ZMQContext();
        $this->socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
    }

    /**
     * @param PusherData $data
     */
    public function send(PusherData $data): void
    {
        $this->socket->connect(self::ADDRESS);
        $this->socket->send(\json_encode($data->toArray()));
        $this->socket->disconnect(self::ADDRESS);
    }
}
