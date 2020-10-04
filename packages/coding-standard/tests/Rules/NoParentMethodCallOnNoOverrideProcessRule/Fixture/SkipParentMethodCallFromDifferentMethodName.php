<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source\ParentClass;

final class SkipParentMethodCallFromDifferentMethodName extends ParentClass
{
    protected function foo(): void
    {
        parent::setUp();
    }
}
