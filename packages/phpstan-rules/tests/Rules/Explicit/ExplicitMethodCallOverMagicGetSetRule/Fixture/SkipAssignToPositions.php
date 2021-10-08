<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source\SomeObjectWithMagicSet;

final class SkipAssignToPositions
{
    public function run(SomeObjectWithMagicSet $someSmartObject)
    {
        $someSmartObject->assignedProperty = 1000;
    }
}
