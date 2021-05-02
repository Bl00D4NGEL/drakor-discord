<?php

declare(strict_types=1);


namespace App\Service\DrakorClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class DrakorClient
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
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
                    'Cookie' => sprintf(' PHPSESSID=%s', $request->authorization)
                ],
                'body' => $request->additionalData
            ]
        );
    }
}
