<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\Source\ParentClass;

final class SkipParentMethodCallFromDifferentMethodName extends ParentClass
{
    protected function foo(): void
    {
        parent::setUp();
    }
}
