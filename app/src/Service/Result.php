<?php

declare(strict_types=1);

namespace App\Service;

final class Result
{
    public string $time = '';
    public int $materialId = 0;
    public string $materialName = '';
    public string $materialRarity = '';
    public int $amount = 0;
    public array $buffs = [];
    public int $gainedExperience = 0;
}
