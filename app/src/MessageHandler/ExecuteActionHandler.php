<?php

declare(strict_types=1);


namespace App\MessageHandler;

use App\Message\AnalyseActionResult;
use App\Message\ExecuteAction;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExecuteActionHandler implements MessageHandlerInterface
{
    private HttpClientInterface $httpClient;
    private MessageBusInterface $messageBus;

    public function __construct(HttpClientInterface $httpClient, MessageBusInterface $messageBus)
    {
        $this->httpClient = $httpClient;
        $this->messageBus = $messageBus;
    }

    public function __invoke(ExecuteAction $executeAction): void
    {
        $postData = [];
        if ($executeAction->rangeFrom() > 0 && $executeAction->rangeTo() > 0) {
            $postData = array_merge(
                $postData,
                [
                    'minRange' => $executeAction->rangeFrom(),
                    'maxRange' => $executeAction->rangeTo()
                ]
            );
        }
        $response = $this->httpClient->request(
            'POST',
            sprintf(
                'https://drakor.com/world/action_%s/%d/%s',
                $executeAction->action(),
                $executeAction->locationId(),
                $executeAction->locationChecksum()
            ),
            [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Cookie' => sprintf(' PHPSESSID=%s', $executeAction->phpSessionId())
                ],
                'body' => $postData
            ]
        );

        $analyseAction = new AnalyseActionResult($executeAction, $response->getContent());
        $this->messageBus->dispatch($analyseAction);
    }
}
