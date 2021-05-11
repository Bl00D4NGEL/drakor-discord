<?php

declare(strict_types=1);


namespace App\MessageHandler;

use App\Entity\LogEntry;
use App\Message\AnalyseActionResult;
use App\Message\ExecuteAction;
use App\Repository\WorkerRepository;
use App\Service\DrakorClient\DrakorClient;
use App\Service\DrakorClient\WorldActionRequest;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class ExecuteActionHandler implements MessageHandlerInterface
{
    private MessageBusInterface $messageBus;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private WorkerRepository $workerRepository;
    private DrakorClient $drakorClient;

    public function __construct(
        DrakorClient $drakorClient,
        MessageBusInterface $messageBus,
        EntityManagerInterface $entityManager,
        WorkerRepository $workerRepository,
        LoggerInterface $logger,
        SerializerInterface $serializer
    )
    {
        $this->messageBus = $messageBus;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->workerRepository = $workerRepository;
        $this->drakorClient = $drakorClient;
    }

    public function __invoke(ExecuteAction $executeAction): void
    {
        if (!$this->shouldExecuteAction($executeAction)) {
            return;
        }

        $response = $this->drakorClient->executeWorldAction(
            $this->createWorldActionRequest($executeAction)
        );

        $logEntry = new LogEntry();
        $logEntry->setRawResult($response->getContent());
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();

        $analyseAction = new AnalyseActionResult($executeAction, $logEntry->getId());
        $this->messageBus->dispatch($analyseAction);
    }

    private function shouldExecuteAction(ExecuteAction $executeAction): bool
    {
        if (count($this->workerRepository->findAll()) === 0) {
            return true;
        }

        $serializedAction = $this->serializer->serialize($executeAction, 'json');
        $worker = $this->workerRepository->findBy(['nextScheduledAction' => $serializedAction]);
        if (count($worker) !== 1) {
            $this->logger->debug(sprintf('Did not find worker for action: %s', $serializedAction));
            return false;
        }

        $this->logger->debug(sprintf('Found worker %d', $worker[0]->getId()));
        $this->entityManager->remove($worker[0]);

        return true;
    }

    #[Pure] protected function createWorldActionRequest(ExecuteAction $executeAction): WorldActionRequest
    {
        $worldActionRequest = new WorldActionRequest();
        $worldActionRequest->action = $executeAction->action;
        $worldActionRequest->locationChecksum = $executeAction->locationChecksum;
        $worldActionRequest->locationId = $executeAction->locationId;
        if ($executeAction->rangeFrom > 0 && $executeAction->rangeTo > 0) {
            $worldActionRequest->additionalData = [
                'minRange' => $executeAction->rangeFrom,
                'maxRange' => $executeAction->rangeTo
            ];
        }
        return $worldActionRequest;
    }
}
