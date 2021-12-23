<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

use Amateri\Chat\Enum\ChatRoomCategory;
use Amateri\Chat\Enum\ChatRoomType;

final class SkipAboveSameCompare
{
    public function run(object $search)
    {
        $cond = [];
        if ($search->getIds() !== null) {
            $cond[] = $search->getIds();
        }
        if ($search->getNames() === false) {
            $cond[] = ['names'];
        }
        return $cond;
    }
}
