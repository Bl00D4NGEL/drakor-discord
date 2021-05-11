<?php

declare(strict_types=1);


namespace App\Controller;

use App\Message\ExecuteAction;
use App\ValueObject\Location;
use App\ValueObject\LocationProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

final class GenerateActionController
{
    private LocationProvider $locationProvider;
    private MessageBusInterface $messageBus;

    public function __construct(LocationProvider $locationProvider, MessageBusInterface $messageBus)
    {
        $this->locationProvider = $locationProvider;
        $this->messageBus = $messageBus;
    }

    /**
     * @Route("/create/action", methods={"POST"})
     */
    public function __invoke(Request $request): Response
    {
        $guildNodes = $this->locationProvider->getGuildNodes();
        $locationId = $request->get('location');
        $filteredGuildNodes = array_filter($guildNodes, static fn(Location $location) => $location->id === (int)$locationId);
        if (count($filteredGuildNodes) !== 1) {
            return new JsonResponse([
                'error' => 'location not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $location = array_values($filteredGuildNodes)[0];

        $command = new ExecuteAction(
            $location->id,
            $location->checksum,
            $location->skill,
            $location->rangeFrom,
            $location->rangeTo
        );
        $this->messageBus->dispatch($command);
        // Load data from post request
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
