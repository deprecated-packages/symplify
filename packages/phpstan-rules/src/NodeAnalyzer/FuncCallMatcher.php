<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use Symplify\Astral\Naming\SimpleNameResolver;

final class FuncCallMatcher
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @param string[] $call
     */
    public function isFuncCallToCallMatch(FuncCall $funcCall, Scope $scope, string $functionName, array $call): bool
    {
        if (! $this->simpleNameResolver->isName($funcCall->name, $functionName)) {
            return false;
        }

        return ! $this->isInDesiredMethod($scope, $call[0], $call[1]);
    }

    private function isInDesiredMethod(Scope $scope, string $class, string $method): bool
    {
        $function = $scope->getFunction();
        if (! $function instanceof MethodReflection) {
            return false;
        }

        if ($function->getName() !== $method) {
            return false;
        }

        $classReflection = $function->getDeclaringClass();
        return $classReflection->getName() === $class;
    }
}
