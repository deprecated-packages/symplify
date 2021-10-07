<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source\SomeObjectWithMagicGet;

final class MagicGetter
{
    public function run(SomeObjectWithMagicGet $someSmartObject)
    {
        return $someSmartObject->id;
    }
}
