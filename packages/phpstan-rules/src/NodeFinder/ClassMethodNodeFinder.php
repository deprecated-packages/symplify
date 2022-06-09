<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeFinder;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\Reflection\ReflectionParser;

final class ClassMethodNodeFinder
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ReflectionParser $reflectionParser,
    ) {
    }

    public function findByMethodCall(MethodCall $methodCall, Scope $scope): ?ClassMethod
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $classLike = $this->reflectionParser->parseClassReflection($classReflection);
        if (! $classLike instanceof Class_) {
            return null;
        }

        /** @var string|null $methodCallName */
        $methodCallName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodCallName === null) {
            return null;
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $classLike->getMethod($methodCallName);
        if (! $classMethod instanceof ClassMethod) {
            return null;
        }

        if (! $classMethod->isPrivate()) {
            return null;
        }

        return $classMethod;
    }
}
