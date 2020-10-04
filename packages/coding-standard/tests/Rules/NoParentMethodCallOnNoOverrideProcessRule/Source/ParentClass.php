<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source;

abstract class ParentClass
{
    protected function setUp(): void
    {
    }
}
