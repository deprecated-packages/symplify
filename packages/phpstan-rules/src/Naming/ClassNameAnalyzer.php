<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Naming;

use Nette\Utils\Strings;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;

final class ClassNameAnalyzer
{
    /**
     * @var string
     * @see https://regex101.com/r/7ovP7N/1
     */
    private const VALUE_OBJECT_REGEX = '#\bValueObject\b#';

    public function isFactoryClassOrMethod(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection instanceof ClassReflection && Strings::endsWith($classReflection->getName(), 'Factory')) {
            return true;
        }

        $function = $scope->getFunction();
        return $function instanceof MethodReflection && Strings::startsWith($function->getName(), 'create');
    }

    public function isValueObjectClass(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return (bool) Strings::match($classReflection->getName(), self::VALUE_OBJECT_REGEX);
    }
}
