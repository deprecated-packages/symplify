<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

final class ParentMethodCallFromDifferentMethodName extends ParentClass
{
    protected function foo(): void
    {
        parent::setUp();
    }
}
