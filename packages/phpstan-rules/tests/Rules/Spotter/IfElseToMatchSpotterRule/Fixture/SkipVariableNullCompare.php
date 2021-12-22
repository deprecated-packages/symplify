<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipVariableNullCompare
{
    private $value;

    public function run()
    {
        $result = 1500;
        if ($result === null) {
            $result = 100;
        }

        return $result;
    }
}
