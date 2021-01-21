<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenThisArgumentRule\Fixture;

use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class SkipReflectionCalling
{
    public function run(PrivatesCaller $privatesCaller)
    {
        $privatesCaller->callPrivateMethod($this, 'run', []);
    }
}
