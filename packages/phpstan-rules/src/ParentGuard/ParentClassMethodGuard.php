<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard;

use PhpParser\Node\Expr\Closure;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentGuard\ParentElementResolver\ParentMethodResolver;

final class ParentClassMethodGuard
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ParentMethodResolver $parentMethodResolver
    ) {
    }

    /**
     * @param ClassMethod|Function_|Closure $functionLike
     */
    public function isFunctionLikeProtected(FunctionLike $functionLike, Scope $scope): bool
    {
        if (! $functionLike instanceof ClassMethod) {
            return false;
        }

        $classMethodName = $this->simpleNameResolver->getName($functionLike);
        if ($classMethodName === null) {
            return false;
        }

        $phpMethodReflection = $this->parentMethodResolver->resolve($scope, $classMethodName);
        return $phpMethodReflection instanceof PhpMethodReflection;
    }
}
