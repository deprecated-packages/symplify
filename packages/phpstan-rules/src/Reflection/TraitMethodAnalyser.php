<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Stmt\Class_;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\Naming\SimpleNameResolver;

final class TraitMethodAnalyser
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(SimpleNameResolver $simpleNameResolver, ReflectionProvider $reflectionProvider)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->reflectionProvider = $reflectionProvider;
    }

    public function doesMethodExistInClassTraits(Class_ $class, string $methodName): bool
    {
        $className = $this->simpleNameResolver->getName($class);
        if ($className === null) {
            return false;
        }

        $usedTraits = class_uses($className);
        if ($usedTraits === false) {
            return false;
        }

        foreach ($usedTraits as $trait) {
            $traitReflectoin = $this->reflectionProvider->getClass($trait);
            if ($traitReflectoin->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
