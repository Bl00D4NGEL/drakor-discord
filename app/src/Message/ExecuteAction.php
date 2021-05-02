<?php

declare(strict_types=1);


namespace App\Message;


final class ExecuteAction
{
    private string $phpSessionId;
    private int $locationId;
    private string $locationChecksum;
    private string $action;
    private int $rangeFrom;
    private int $rangeTo;

    public function __construct(int $locationId, string $locationChecksum, string $action, int $rangeFrom, int $rangeTo, string $phpSessionId)
    {
        $this->locationId = $locationId;
        $this->locationChecksum = $locationChecksum;
        $this->action = $action;
        $this->rangeFrom = $rangeFrom;
        $this->rangeTo = $rangeTo;
        $this->phpSessionId = $phpSessionId;
    }

    public function phpSessionId(): string
    {
        return $this->phpSessionId;
    }

    public function locationId(): int
    {
        return $this->locationId;
    }

    public function locationChecksum(): string
    {
        return $this->locationChecksum;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function rangeFrom(): int
    {
        return $this->rangeFrom;
    }

    public function rangeTo(): int
    {
        return $this->rangeTo;
    }


}
