<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source;

abstract class ParentClass
{
    protected function setUp(): void
    {
    }
}
