<?php

declare(strict_types=1);


namespace App\Service\DrakorClient;

final class WorldActionRequest
{
    public string $action = '';
    public int $locationId = 0;
    public string $locationChecksum = '';
    public string $authorization = '';
    public array $additionalData = [];
}
