<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Stmt\Class_;
use ReflectionClass;
use Symplify\Astral\Naming\SimpleNameResolver;

final class TraitMethodAnalyser
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function doesMethodExistInClassTraits(Class_ $class, string $methodName): bool
    {
        $className = $this->simpleNameResolver->getName($class);
        if ($className === null) {
            return false;
        }

        /** @var string[] $usedTraits */
        $usedTraits = (array) class_uses($className);

        foreach ($usedTraits as $trait) {
            $reflectionClass = new ReflectionClass($trait);
            if ($reflectionClass->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
