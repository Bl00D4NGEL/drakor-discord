<?php

declare(strict_types=1);


namespace App\ValueObject;

final class Location
{
    public int $id;
    public string $checksum;
    public string $skill;
    public string $name;
    public int $rangeFrom;
    public int $rangeTo;

    public function __construct(int $id, string $checksum, string $skill, string $name, int $rangeFrom = 0, int $rangeTo = 0)
    {
        $this->id = $id;
        $this->checksum = $checksum;
        $this->skill = $skill;
        $this->name = $name;
        $this->rangeFrom = $rangeFrom;
        $this->rangeTo = $rangeTo;
    }
}
