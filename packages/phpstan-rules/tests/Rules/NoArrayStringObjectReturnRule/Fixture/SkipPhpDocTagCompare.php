<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoArrayStringObjectReturnRule\Fixture;

use PHPStan\PhpDoc\ResolvedPhpDocBlock;
use PHPStan\Reflection\ClassReflection;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class SkipPhpDocTagCompare
{
    public function run(PrivatesCaller $privatesCaller, ClassReflection $classReflection)
    {
        $resolvedPhpDocBlock = $privatesCaller->callPrivateMethod($classReflection, 'getResolvedPhpDoc', []);
        if (! $resolvedPhpDocBlock instanceof ResolvedPhpDocBlock) {
            return false;
        }

        return $resolvedPhpDocBlock->getExtendsTags() !== [];
    }
}
