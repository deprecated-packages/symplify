<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\SomeInterface;

final class ThisPassedFromInterface
{
    public function run(SomeInterface $obj)
    {
    }
}
