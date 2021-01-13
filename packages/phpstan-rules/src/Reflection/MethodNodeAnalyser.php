<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\PHPStanRules\ValueObject\MethodName;

final class MethodNodeAnalyser
{
    public function isInConstructor(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === MethodName::CONSTRUCTOR;
    }
}
