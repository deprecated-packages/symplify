<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoArrayAccessOnObjectRule\Fixture;

use SplFixedArray;

final class SkipSplFixedArray
{
    public function run(SplFixedArray $splFixedArray)
    {
        return $splFixedArray[0];
    }
}
