<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

final class ParentMethodCallInsideExpression extends ParentClass
{
    protected function setUp(): void
    {
        if (true) {
            parent::setUp();
        }
    }
}
