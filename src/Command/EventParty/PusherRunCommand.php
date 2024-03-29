<?php declare(strict_types=1);

namespace App\Command\EventParty;

use App\Component\Messaging\EventParty\Pusher;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PusherRunCommand extends Command
{
    protected static $defaultName = 'pusher:run';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('push start');

        $loop   = Factory::create();
        $pusher = new Pusher($this->em);

        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new Context($loop);
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', [$pusher, 'onMessage']);

        // Set up our WebSocket server for clients wanting real-time updates
        $webSock = new Server('0.0.0.0:8888/pusher', $loop); // Binding to 0.0.0.0 means remotes can connect

        new IoServer(new HttpServer(new WsServer(new WampServer($pusher))), $webSock);

        $loop->run();
    }
}
