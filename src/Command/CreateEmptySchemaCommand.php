<?php declare(strict_types=1);

namespace App\Command;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEmptySchemaCommand extends Command
{
    protected static $defaultName = 'app:empty_schema:create';

    /** @var EntityManagerInterface */
    private $em;

    /** @var ORMPurger */
    private $purger;

    /** @var OutputInterface */
    private $output;

    /** @var Application */
    private $app;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->purger = new ORMPurger($this->em);
        $this->output = $output;
        $this->app    = $this->getApplication();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runCommand('doctrine:database:create --if-not-exists');
        $this->purger->purge(); // clear tables data
        $this->runCommand('doctrine:schema:update --no-interaction');
    }

    /**
     * @param string $commandStr
     */
    private function runCommand(string $commandStr): void
    {
        $commandPieces = explode(' ', $commandStr);
        $commandName   = array_shift($commandPieces);
        
        $commandArguments = ['command' => $commandName];
        foreach ($commandPieces as $arguments) {
            $arguments = explode('=', $arguments);

            $argument = $arguments[0];
            $value    = $arguments[1] ?? true;

            $commandArguments[$argument] = $value;
        }

        $argumentsInput = new ArrayInput($commandArguments);
        $argumentsInput->setInteractive(false);

        $this->app->find($commandName)->run($argumentsInput, $this->output);
    }
}