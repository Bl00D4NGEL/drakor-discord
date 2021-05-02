<?php

declare(strict_types=1);


namespace App\Command;

use App\Message\ExecuteAction;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class StartActionCommand extends Command
{
    private MessageBusInterface $messageBus;
    private string $drakorPhpSessionId = '';

    public function __construct(MessageBusInterface $messageBus, string $drakorPhpSessionId)
    {
        $this->messageBus = $messageBus;
        $this->drakorPhpSessionId = $drakorPhpSessionId;
        parent::__construct();
    }

    protected static $defaultName = 'drakor:start:action';

    protected function configure()
    {
        $this
            ->addArgument('locationId', InputArgument::REQUIRED, 'The location id')
            ->addArgument('locationChecksum', InputArgument::REQUIRED, 'The location checksum (32 characters long)')
            ->addArgument('action', InputArgument::REQUIRED, 'The action you want to execute')
            ->addArgument('rangeFrom', InputArgument::OPTIONAL, 'Optional range for guild node', 0)
            ->addArgument('rangeTo', InputArgument::OPTIONAL, 'Optional range for guild node', 0)
            ->addArgument('phpSessionId', InputArgument::OPTIONAL, 'PHPSESSID cookie value', $this->drakorPhpSessionId);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = new ExecuteAction(
            (int)$input->getArgument('locationId'),
            $input->getArgument('locationChecksum'),
            $input->getArgument('action'),
            (int)$input->getArgument('rangeFrom'),
            (int)$input->getArgument('rangeTo'),
            $input->getArgument('phpSessionId')
        );
        $this->messageBus->dispatch($command);

        return Command::SUCCESS;
    }
}
