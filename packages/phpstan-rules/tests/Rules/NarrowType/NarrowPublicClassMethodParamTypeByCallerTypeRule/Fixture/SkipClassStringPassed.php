<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

final class SkipClassStringPassed
{
    public function resolve(string $className)
    {
    }
}
