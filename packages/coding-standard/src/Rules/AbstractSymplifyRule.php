<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

abstract class AbstractSymplifyRule implements Rule
{
    public function getShortClassName(Scope $scope): ?string
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        $className = $classReflection->getName();
        if (! Strings::contains($className, '\\')) {
            return $className;
        }

        return (string) Strings::after($className, '\\', - 1);
    }

    public function getClassName(Scope $scope): ?string
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        return $classReflection->getName();
    }
}
