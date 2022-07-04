<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Source;

use Symplify\PHPStanRules\Tests\Rules\DeadCode\UnusedPublicClassMethodRule\Fixture\SkipUsedPublicMethod;

final class ClassMethodCaller
{
    private function go(SkipUsedPublicMethod $usedPublicMethod)
    {
        $usedPublicMethod->useMe();
    }
}
