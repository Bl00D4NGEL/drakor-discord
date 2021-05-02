<?php

declare(strict_types=1);


namespace App\MessageHandler;

use App\Message\AnalyseActionResult;
use App\Service\ResultAnalyser;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class AnalyseActionResultHandler implements MessageHandlerInterface
{
    private ResultAnalyser $resultAnalyser;
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;

    public function __construct(ResultAnalyser $resultAnalyser, MessageBusInterface $messageBus, LoggerInterface $logger)
    {
        $this->resultAnalyser = $resultAnalyser;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
    }

    public function __invoke(AnalyseActionResult $analyseActionResult): void
    {
        $input = $analyseActionResult->actionResult();
        if ($this->resultAnalyser->isNodeDepleted($input)) {
            $this->logger->info('Node has been depleted. Stopping now.');
            return;
        }

        if ($this->resultAnalyser->hasError($input)) {
            $this->logger->error('The result seems to be erroneous. Please check: ' . $input);
            return;
        }

        // TODO do something with $result and $food?
        $result = $this->resultAnalyser->parseResult($input);
        $food = $this->resultAnalyser->parseFood($input);

        $timeToNextAction = $this->resultAnalyser->getTimeUntilNextAction($input);
        $this->logger->info(
            sprintf(
                'Starting next action in %d seconds', $timeToNextAction / 1000
            )
        );
        $envelope = new Envelope($analyseActionResult->initialAction(), [
            new DelayStamp($timeToNextAction)
        ]);
        $this->messageBus->dispatch($envelope);
    }
}
