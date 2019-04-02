<?php declare(strict_types=1);

namespace App\Command\EventParty;

use App\Component\Messaging\EventParty\Chat;
use Doctrine\ORM\EntityManagerInterface;
use Ratchet\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChatRunCommand extends Command
{
    protected static $defaultName = 'event_party:chat:run';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('chat start');

        $chat = new Chat($this->em);

        $app = new App();
        $app->route('/chat', $chat);

        $app->run();
    }
}
