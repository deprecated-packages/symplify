<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\NodeFinder\MethodCallNodeFinder;

final class MethodCallArgValueResolver
{
    /**
     * @var MethodCallNodeFinder
     */
    private $methodCallNodeFinder;

    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

    public function __construct(MethodCallNodeFinder $methodCallNodeFinder, NodeValueResolver $nodeValueResolver)
    {
        $this->methodCallNodeFinder = $methodCallNodeFinder;
        $this->nodeValueResolver = $nodeValueResolver;
    }

    /**
     * @return string[]
     */
    public function resolveFirstArgInMethodCalls(Class_ $class, Scope $scope, string $methodName): array
    {
        $methodCalls = $this->methodCallNodeFinder->findByName($class, $methodName);
        return $this->resolveFirstArgValues($methodCalls, $scope);
    }

    /**
     * @param MethodCall[] $methodCalls
     * @return string[]
     */
    private function resolveFirstArgValues(array $methodCalls, Scope $scope): array
    {
        $names = [];

        foreach ($methodCalls as $methodCall) {
            $firstArgValue = $methodCall->args[0]->value;
            $resolvedValue = $this->nodeValueResolver->resolve($firstArgValue, $scope->getFile());
            if (! is_string($resolvedValue)) {
                continue;
            }

            $names[] = $resolvedValue;
        }

        return $names;
    }
}
