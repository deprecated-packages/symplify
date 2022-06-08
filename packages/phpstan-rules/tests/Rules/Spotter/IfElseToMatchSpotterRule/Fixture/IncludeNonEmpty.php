<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class IncludeNonEmpty
{
    public function run(int $value)
    {
        $data = [];

        if ($value === 1000) {
            $data[] = 1000;
        } elseif ($value === 200) {
            $data[] = 40;
        } else {
            $data[] = 10000;
        }

        return $data;
    }
}
