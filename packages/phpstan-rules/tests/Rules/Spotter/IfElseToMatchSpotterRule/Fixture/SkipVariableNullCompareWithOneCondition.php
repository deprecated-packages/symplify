<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\Fixture;

final class SkipVariableNullCompareWithOneCondition
{
    private $value;

    public function run($status)
    {
        $result = 1500;
        if ($result === null && $status = 100) {
            $result = 100;
        }

        return $result;
    }
}
