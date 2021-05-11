<?php

declare(strict_types=1);

namespace App\ValueObject;

use JetBrains\PhpStorm\Pure;

final class LocationProvider
{
    /**
     * @return Location[]
     */
    #[Pure]
    public function getGuildNodes(): array
    {
        return [
            new Location(
                244,
                '1f8d47f684b8e7471f3fdcbb344cd9a30abf8e2793a4948d2b6c086ba5d17bf7',
                'logging',
                'Guild Logging Node',
                130,
                130
            ),
            new Location(
                247,
                'd5504fd8b88ce433ddd31f174cb94360fefe20e5fa9fd5227422da4a0c729159',
                'researching',
                'Guild Researching Node',
                71,
                71
            ),
            new Location(
                237,
                '0797c5824c6474cb921a504e3efff88f7bcfdae6933da46a6618a8f4b3d57e6e',
                'fishing',
                'Guild Fishing Node',
                96,
                96
            ),
            new Location(
                248,
                '7d27d6c23baf055581898865b1e4254442fb66f6726ce48ac7436677cc11df05',
                'gathering',
                'Guild Gathering Node',
                97,
                97
            ),
            new Location(
                245,
                '7ade4a95f849bdee84d014fa89c382a0ad08d9b914b186cb6215d7ce671408c2',
                'mining',
                'Guild Mining Node',
                100,
                100
            )
        ];
    }
}
