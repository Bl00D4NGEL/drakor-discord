<?php

declare(strict_types=1);


namespace App\MessageHandler;

use App\Entity\Worker;
use App\Message\AnalyseActionResult;
use App\Repository\LogEntryRepository;
use App\Service\ResultAnalyser;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Serializer\SerializerInterface;

final class AnalyseActionResultHandler implements MessageHandlerInterface
{
    private ResultAnalyser $resultAnalyser;
    private MessageBusInterface $messageBus;
    private LoggerInterface $logger;
    private LogEntryRepository $logEntryRepository;
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(LogEntryRepository $logEntryRepository, ResultAnalyser $resultAnalyser, MessageBusInterface $messageBus, LoggerInterface $logger, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->resultAnalyser = $resultAnalyser;
        $this->messageBus = $messageBus;
        $this->logger = $logger;
        $this->logEntryRepository = $logEntryRepository;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    public function __invoke(AnalyseActionResult $analyseActionResult): void
    {
        $logEntryId = $analyseActionResult->logEntryId();
        $logEntry = $this->logEntryRepository->find($logEntryId);
        $input = $logEntry->getRawResult();
        if ($this->resultAnalyser->isNodeDepleted($input)) {
            $this->logger->info('Node has been depleted. Stopping now.');
            return;
        }

        if ($this->resultAnalyser->hasError($input)) {
            $this->logger->error('The result seems to be erroneous. Please check: ' . $input);
            return;
        }

        $timeToNextAction = $this->resultAnalyser->getTimeUntilNextAction($input);
        $this->logger->info(
            sprintf(
                'Starting next action in %d seconds', $timeToNextAction / 1000
            )
        );

        $worker = new Worker();
        $worker->setNextScheduledAction($this->serializer->serialize($analyseActionResult->initialAction(), 'json'));
        $this->entityManager->persist($worker);
        $this->entityManager->flush();
        $envelope = new Envelope($analyseActionResult->initialAction(), [
            new DelayStamp($timeToNextAction)
        ]);
        $this->messageBus->dispatch($envelope);
    }
}
