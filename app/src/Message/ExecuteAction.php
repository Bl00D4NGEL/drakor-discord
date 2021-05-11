<?php

declare(strict_types=1);

namespace App\Message;

use DateTimeImmutable;
use DateTimeInterface;

final class ExecuteAction
{
    public int $locationId;
    public string $locationChecksum;
    public string $action;
    public int $rangeFrom;
    public int $rangeTo;
    public DateTimeInterface $creationTime;

    public function __construct(int $locationId, string $locationChecksum, string $action, int $rangeFrom, int $rangeTo)
    {
        $this->locationId = $locationId;
        $this->locationChecksum = $locationChecksum;
        $this->action = $action;
        $this->rangeFrom = $rangeFrom;
        $this->rangeTo = $rangeTo;
        $this->creationTime = new DateTimeImmutable();
    }
}
