<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\Nette;

use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

final class UsedLocaComponentNamesResolver
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(SimpleNameResolver $simpleNameResolver, NodeFinder $nodeFinder)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function resolveFromClassMethod(ClassMethod $classMethod): array
    {
        $parent = $classMethod->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Class_) {
            return [];
        }

        $getComponentNames = $this->resolveThisGetComponentArguments($parent);
        $dimFetchNames = $this->resolveDimFetchArguments($parent);

        return array_merge($getComponentNames, $dimFetchNames);
    }

    /**
     * @return string[]
     */
    private function resolveThisGetComponentArguments(Class_ $class): array
    {
        $componentNames = [];

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($class, MethodCall::class);
        foreach ($methodCalls as $methodCall) {
            if (! $methodCall->var instanceof Variable) {
                continue;
            }

            if (! $this->simpleNameResolver->isName($methodCall->var, 'this')) {
                continue;
            }

            if (! $this->simpleNameResolver->isName($methodCall->name, 'getComponent')) {
                continue;
            }

            $firstArg = $methodCall->args[0];

            $firstArgValue = $firstArg->value;
            if (! $firstArgValue instanceof String_) {
                continue;
            }

            $componentNames[] = $firstArgValue->value;
        }

        return $componentNames;
    }

    /**
     * @return string[]
     */
    private function resolveDimFetchArguments(Class_ $class): array
    {
        $componentNames = [];

        /** @var ArrayDimFetch[] $arrayDimFetches */
        $arrayDimFetches = $this->nodeFinder->findInstanceOf($class, ArrayDimFetch::class);
        foreach ($arrayDimFetches as $arrayDimFetch) {
            if (! $arrayDimFetch->var instanceof Variable) {
                continue;
            }

            if (! $this->simpleNameResolver->isName($arrayDimFetch->var, 'this')) {
                continue;
            }

            if (! $arrayDimFetch->dim instanceof String_) {
                continue;
            }

            $componentNames[] = $arrayDimFetch->dim->value;
        }

        return $componentNames;
    }
}
