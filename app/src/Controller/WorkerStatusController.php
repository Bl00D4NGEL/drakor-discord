<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\Worker;
use App\Message\ExecuteAction;
use App\Repository\WorkerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class WorkerStatusController extends AbstractController
{
    private WorkerRepository $workerRepository;
    private SerializerInterface $serializer;

    public function __construct(
        WorkerRepository $workerRepository,
        SerializerInterface $serializer
    )
    {
        $this->workerRepository = $workerRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/worker-status", name="worker_status")
     */
    public function __invoke(): Response
    {
        return $this->render('worker-status.html.twig', [
            'workers' => array_map(function (Worker $worker): ExecuteAction {
                return $this->serializer->deserialize($worker->getNextScheduledAction(), ExecuteAction::class, 'json');
            }, $this->workerRepository->findAll()),
        ]);
    }
}
