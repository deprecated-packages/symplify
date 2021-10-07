<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source\NormalObject;

final class NormalPropertyFetch
{
    public function run(NormalObject $normalObject)
    {
        return $normalObject->normalProperty;
    }
}
