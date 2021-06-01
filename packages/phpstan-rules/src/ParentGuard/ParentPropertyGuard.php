<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard;

use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;

final class ParentPropertyGuard
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isPropertyGuarded(Property $property, Scope $scope): bool
    {
        $propertyName = $this->simpleNameResolver->getName($property);
        if ($propertyName === null) {
            return false;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClassReflectoin) {
            if (! $parentClassReflectoin->hasProperty($propertyName)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
