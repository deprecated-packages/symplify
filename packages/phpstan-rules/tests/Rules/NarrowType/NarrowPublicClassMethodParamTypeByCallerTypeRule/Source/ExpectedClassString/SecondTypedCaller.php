<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Source\ExpectedClassString;

use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Tests\Rules\NarrowType\NarrowPublicClassMethodParamTypeByCallerTypeRule\Fixture\SkipClassStringPassed;

final class SecondTypedCaller
{
    public function run(ClassReflection $classReflection)
    {
        $skipClassStringPassed = new SkipClassStringPassed();
        $skipClassStringPassed->resolve($classReflection->getName());
    }
}
