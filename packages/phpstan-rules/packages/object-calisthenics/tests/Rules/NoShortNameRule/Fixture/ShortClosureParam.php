<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoShortNameRule\Fixture;

final class ShortClosureParam
{
    public function run()
    {
        $result = function (int $n) {
            return 1;
        };
    }
}
