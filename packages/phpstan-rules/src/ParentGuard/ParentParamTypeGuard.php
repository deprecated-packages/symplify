<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\ValueObject\AttributeKey;

final class ParentParamTypeGuard
{
    public function isRequiredByContract(Node $node, Scope $scope): bool
    {
        $parent = $node->getAttribute(AttributeKey::PARENT);
        if (! $parent instanceof Param) {
            return false;
        }

        // possibly protected by parent class
        $parentParent = $parent->getAttribute(AttributeKey::PARENT);
        if (! $parentParent instanceof ClassMethod) {
            return false;
        }

        /** @var string $methodName */
        $methodName = (string) $parentParent->name;

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        $parentClassLikes = array_merge($classReflection->getInterfaces(), $classReflection->getParents());

        foreach ($parentClassLikes as $parentClassLike) {
            if ($parentClassLike->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
