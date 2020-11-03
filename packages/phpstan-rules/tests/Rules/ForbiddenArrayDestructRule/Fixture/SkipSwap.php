<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\Fixture;

final class SkipSwap
{
    public function run($one, $two)
    {
        [$one, $two] = [$two, $one];
    }
}
