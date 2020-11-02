<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\StaticCallExplicit;

final class SkipStaticMask
{
    public function run()
    {
        StaticCallExplicit::honestlyStatic();
    }
}
