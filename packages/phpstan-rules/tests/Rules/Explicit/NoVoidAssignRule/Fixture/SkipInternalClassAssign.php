<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\Fixture;

final class SkipInternalClassAssign
{
    public function run(\ReflectionParameter $reflectionParameter)
    {
        $type = $reflectionParameter->getType();

        $isPromoted = $reflectionParameter->isPromoted();
    }
}
