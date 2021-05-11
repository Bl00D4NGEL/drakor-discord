<?php

declare(strict_types=1);


namespace App\Service\DrakorClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class DrakorClient
{
    private HttpClientInterface $httpClient;
    private string $authorization;

    public function __construct(HttpClientInterface $httpClient, string $authorization)
    {
        $this->httpClient = $httpClient;
        $this->authorization = $authorization;
    }

    public function executeWorldAction(WorldActionRequest $request): ResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            sprintf(
                'https://drakor.com/world/action_%s/%d/%s',
                $request->action,
                $request->locationId,
                $request->locationChecksum
            ),
            [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Cookie' => sprintf(' PHPSESSID=%s', $this->authorization)
                ],
                'body' => $request->additionalData
            ]
        );
    }

    public function travelToLocation(int $locationId): ResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            sprintf(
                'https://drakor.com/world/travel/%d',
                $locationId,
            ),
            [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Cookie' => sprintf(' PHPSESSID=%s', $this->authorization)
                ],
            ]
        );
    }
}
