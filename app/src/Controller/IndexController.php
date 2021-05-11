<?php

declare(strict_types=1);


namespace App\Controller;

use App\Entity\LogEntry;
use App\Repository\LogEntryRepository;
use App\Service\Result;
use App\Service\ResultAnalyser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{
    private LogEntryRepository $logEntryRepository;
    private ResultAnalyser $resultAnalyser;

    public function __construct(
        LogEntryRepository $logEntryRepository,
        ResultAnalyser $resultAnalyser
    )
    {
        $this->logEntryRepository = $logEntryRepository;
        $this->resultAnalyser = $resultAnalyser;
    }

    /**
     * @Route("/", name="index")
     */
    public function __invoke(): Response
    {
        $logEntries = $this->logEntryRepository->findBy([], ['creationTime' => 'DESC'], 50);
        $results = array_map(function (LogEntry $logEntry) {
            return $this->resultAnalyser->parseResult($logEntry->getRawResult());
        }, $logEntries);
        $filteredResults = array_filter($results, static function (Result $result) {
            return $result->time !== '';
        });
        return $this->render('index.html.twig', [
            'results' => array_map(static function (Result $result) {
                $result->materialRarity = strtolower($result->materialRarity);
                return $result;
            }, $filteredResults),
        ]);
    }
}
