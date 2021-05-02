<?php

declare(strict_types=1);


namespace App\Message;

final class AnalyseActionResult
{
    private int $logEntryId;
    private ExecuteAction $initialAction;

    public function __construct(ExecuteAction $initialAction, int $logEntryId)
    {
        $this->logEntryId = $logEntryId;
        $this->initialAction = $initialAction;
    }

    public function logEntryId(): int
    {
        return $this->logEntryId;
    }

    public function initialAction(): ExecuteAction
    {
        return $this->initialAction;
    }
}
