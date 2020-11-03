<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\Source\StaticCallExplicit;

final class SkipStaticMask
{
    public function run()
    {
        StaticCallExplicit::honestlyStatic();
    }
}
