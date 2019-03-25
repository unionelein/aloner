<?php

namespace App\Command;

use App\Component\Messaging\Chat;
use App\Component\Messaging\Announcement;
use Ratchet\App;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ChatRunCommand extends Command
{
    protected static $defaultName = 'app:chat:run';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('chat start');

        $app = new App();
        $app->route('/chat', new Chat());

        $app->run();
    }
}
