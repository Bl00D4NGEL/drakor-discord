<?php

declare(strict_types=1);


namespace App\Service;


use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class ResultAnalyser implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function getTimeUntilNextAction(string $input): int
    {
        if (1 === preg_match('/startTimer\((\d+)/', $input, $matches)) {
            return (int)$matches[1];
        }
        return 0;
    }

    public function parseResult(string $input): Result
    {
        $result = new Result();
        if (preg_match('/([0-9,]+) EXP/', $input, $expMatch)) {
            $result->gainedExperience = (int)str_replace(',', '', $expMatch[1]);
        }
        if (preg_match('/<span class="hourMin xsmall">(.*?)<\/span>/', $input, $timeMatch)) {
            $result->time = $timeMatch[1];
        }
        // Pattern is "Created ..." instead of "You found ..."
        if (preg_match('/You <\/span>found <span id="viewmat-(\d+)" class="(\w+) viewMat">\[(.*?)\]<\/span>.*?x(\d+)/', $input, $resultMatch)) {
            $result->materialId = (int)$resultMatch[1];
            $result->materialRarity = $resultMatch[2];
            $result->materialName = $resultMatch[3];
            $result->amount = (int)$resultMatch[4];
        }
        if (preg_match('/\+(\d+) Mastery/', $input, $masteryMatch)) {
            $result->buffs[] = ['Mastery' => (int)$masteryMatch[1]];
        }

        if (preg_match('/<span class="buffValue">\(\+(\d+) (.)R\)<\/span>/', $input, $foodMatch)) {
            $food = $foodMatch[2];
            if ($food === 'D') {
                $food = 'Drop Rate';
            } else if ($food === 'C') {
                $food = 'Create Rate';
            } else {
                if ($this->logger) {
                    $this->logger->error(sprintf('Unknown food type "%s" found.', $food));
                }
            }
            $result->buffs[] = [$food => (int)$foodMatch[1]];
        }

        if (preg_match('/<span class="buffValue">\(\+([0-9,]+) Double Exp\)<\/span>/', $input, $doubleExpMatch)) {
            $result->buffs[] = ['Double Experience' => (int)$doubleExpMatch[1]];
        }

        return $result;
    }

    public function parseFood(string $input): Food
    {
        $food = new Food();
        if (preg_match('/<div class="iconLevel">(\d+)<\/div>/', $input, $levelMatch)) {
            $food->level = (int)$levelMatch[1];
        }
        if (preg_match('/<div id="food(\d+)"/', $input, $idMatch)) {
            $food->id = (int)$idMatch[1];
        }
        if (preg_match('/<img class="centerImage" src="(.*?)" border=0>/', $input, $imageSourceMatch)) {
            $food->imageSrc = $imageSourceMatch[1];
        }
        if (preg_match('/<div id="food\d+" class="drIcon card(\w+) slot_default" >/', $input, $rarityMatch)) {
            $food->rarity = $rarityMatch[1];
        }
        if (preg_match('/<div class="buffRemain \w+">(.*?)<\/div>/', $input, $timeLeftMatch)) {
            $food->timeLeft = $timeLeftMatch[1];
        }
        return $food;
    }

    public function isNodeDepleted(string $input): bool {
        return str_contains($input, 'area has been depleted');
    }

    public function hasError(string $input): bool {
        return str_contains($input, 'An Error has occured');
    }
}
