<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

final class ParentMethodCallNoOverride extends ParentClass
{
    protected function setUp(): void
    {
        parent::setUp();

        // comment
    }
}
