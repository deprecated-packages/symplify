<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

abstract class ParentClass
{
    protected function setUp(): void
    {
    }
}
