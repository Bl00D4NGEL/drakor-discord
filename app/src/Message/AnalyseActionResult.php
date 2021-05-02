<?php

declare(strict_types=1);


namespace App\Message;

final class AnalyseActionResult
{
    private string $actionResult;
    private ExecuteAction $initialAction;

    public function __construct(ExecuteAction $initialAction, string $actionResult)
    {
        $this->actionResult = $actionResult;
        $this->initialAction = $initialAction;
    }

    public function actionResult(): string
    {
        return $this->actionResult;
    }

    public function initialAction(): ExecuteAction
    {
        return $this->initialAction;
    }
}
